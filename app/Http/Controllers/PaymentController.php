<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initiate an ABA PayWay checkout session.
     * Called by Vue frontend (POST /api/checkout).
     * Performs server-to-server request to ABA and returns checkout payload (deeplink/QR).
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.name'  => 'required|string',
            'items.*.qty'   => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $merchantId  = (string) config('payway.merchant_id');
        $apiKey      = (string) config('payway.api_key');
        $apiKeyValidUntil = (string) config('payway.api_key_valid_until', '');
        $currency    = (string) config('payway.currency');
        $paymentOption = (string) config('payway.payment_option', '');
        $type        = (string) config('payway.type', 'purchase');
        $returnUrl   = (string) config('payway.return_url', '');
        $cancelUrl   = (string) config('payway.cancel_url', '');
        $continueSuccessUrl = (string) config('payway.continue_success_url', '');
        $skipSuccessPage = (string) config('payway.skip_success_page', '');
        $lifetimeMinutes = (int) config('payway.lifetime', 30);
        if ($lifetimeMinutes < 3) {
            $lifetimeMinutes = 3;
        }
        if ($lifetimeMinutes > 43200) {
            $lifetimeMinutes = 43200;
        }
        $lifetime = (string) $lifetimeMinutes;
        $checkoutUrl = (string) config('payway.checkout_url', '');

        if (str_contains($paymentOption, ',')) {
            $paymentOption = '';
        }

        if (!str_contains($checkoutUrl, '/api/payment-gateway/v1/payments/purchase')) {
            $checkoutUrl = (string) config('payway.api_url', $checkoutUrl);
        }

        if ($merchantId === '' || $apiKey === '') {
            return response()->json([
                'message' => 'ABA PayWay credentials are missing. Please configure ABA_MERCHANT_ID and ABA_API_KEY in .env.',
            ], 500);
        }

        if ($apiKeyValidUntil !== '') {
            try {
                $expiresAt = Carbon::parse($apiKeyValidUntil, 'UTC')->endOfDay();

                if (now('UTC')->greaterThan($expiresAt)) {
                    return response()->json([
                        'message' => 'ABA PayWay API credential is expired. Please request a new sandbox API access and update ABA_MERCHANT_ID / ABA_API_KEY.',
                    ], 422);
                }
            } catch (\Throwable $e) {
                Log::warning('Invalid ABA_API_KEY_VALID_UNTIL format', [
                    'value' => $apiKeyValidUntil,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $nowUtc = now('UTC');
        $reqTime = $nowUtc->format('YmdHis');
        $tranId      = $reqTime . Str::upper(Str::random(6));

        // Build items array for ABA (name, quantity, price)
        $abaItems = collect($request->items)->map(fn ($item) => [
            'name'     => $item['name'],
            'quantity' => (int) $item['qty'],
            'price'    => number_format((float) $item['price'], 2, '.', ''),
        ])->values()->toArray();

        // Total amount (must match sum of items * qty)
        $amount = collect($request->items)
            ->reduce(fn ($carry, $item) => $carry + ($item['price'] * $item['qty']), 0.0);
        $amount = number_format($amount, 2, '.', '');

        $itemsJson = json_encode($abaItems, JSON_UNESCAPED_UNICODE);
        $itemsBase64 = base64_encode($itemsJson);

        $shipping = '';
        $firstname = '';
        $lastname = '';
        $email = '';
        $phone = '';
        $returnDeeplink = '';
        $customFields = '';
        $returnParams = '';
        $payout = '';
        $additionalParams = '';
        $googlePayToken = '';

        $preHashStr = $reqTime
            . $merchantId
            . $tranId
            . $amount
            . $itemsBase64
            . $shipping
            . $firstname
            . $lastname
            . $email
            . $phone
            . $type
            . $paymentOption
            . $returnUrl
            . $cancelUrl
            . $continueSuccessUrl
            . $returnDeeplink
            . $currency
            . $customFields
            . $returnParams
            . $payout
            . $lifetime
            . $additionalParams
            . $googlePayToken
            . $skipSuccessPage;
        $hash = base64_encode(hash_hmac('sha512', $preHashStr, $apiKey, true));

        Log::info('PayWay checkout request generated', [
            'req_time' => $reqTime,
            'server_utc_iso' => $nowUtc->toIso8601String(),
            'server_timezone' => config('app.timezone'),
            'lifetime_minutes' => $lifetimeMinutes,
            'merchant_id' => $merchantId,
            'tran_id' => $tranId,
            'checkout_url' => $checkoutUrl,
        ]);

        $payload = [
            'merchant_id'          => $merchantId,
            'tran_id'              => $tranId,
            'amount'               => $amount,
            'items'                => $itemsBase64,
            'currency'             => $currency,
            'type'                 => $type,
            'payment_option'       => $paymentOption,
            'req_time'             => $reqTime,
            'return_url'           => $returnUrl,
            'cancel_url'           => $cancelUrl,
            'continue_success_url' => $continueSuccessUrl,
            'skip_success_page'    => $skipSuccessPage,
            'lifetime'             => $lifetime,
            'hash'                 => $hash,
        ];

        try {
            $abaResponse = Http::asForm()
                ->acceptJson()
                ->timeout(30)
                ->post($checkoutUrl, $payload);

            $abaBody = $abaResponse->json();

            if (!$abaResponse->successful() || !is_array($abaBody)) {
                Log::error('PayWay checkout API call failed', [
                    'http_status' => $abaResponse->status(),
                    'response' => $abaResponse->body(),
                    'tran_id' => $tranId,
                ]);

                return response()->json([
                    'message' => 'Could not connect to ABA PayWay checkout service. Please try again.',
                ], 502);
            }

            $statusCode = (string) data_get($abaBody, 'status.code', '');

            if ($statusCode !== '00') {
                Log::warning('PayWay checkout API returned failure', [
                    'tran_id' => $tranId,
                    'status_code' => $statusCode,
                    'response' => $abaBody,
                ]);

                return response()->json([
                    'message' => (string) data_get($abaBody, 'status.message', 'ABA payment request was not accepted.'),
                    'aba_response' => $abaBody,
                ], 422);
            }

            return response()->json([
                'status'           => data_get($abaBody, 'status'),
                'description'      => data_get($abaBody, 'description'),
                'tran_id'          => $tranId,
                'amount'           => $amount,
                'abapay_deeplink'  => data_get($abaBody, 'abapay_deeplink', ''),
                'qrString'         => data_get($abaBody, 'qrString', ''),
                'qrImage'          => data_get($abaBody, 'qrImage', ''),
                'app_store'        => data_get($abaBody, 'app_store', ''),
                'play_store'       => data_get($abaBody, 'play_store', ''),
            ]);
        } catch (\Throwable $e) {
            Log::error('PayWay checkout API exception', [
                'tran_id' => $tranId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Checkout failed while contacting ABA PayWay. Please try again.',
            ], 502);
        }
    }

    /**
     * ABA PayWay redirects the browser back here after payment (success or decline).
     * GET /payment/result
     */
    public function result(Request $request)
    {
        $status  = $request->input('status');   // 0 = success, others = failure
        $tranId  = $request->input('tran_id');
        $amount  = $request->input('apv');

        return view('payment.result', compact('status', 'tranId', 'amount'));
    }

    /**
     * ABA PayWay redirects the browser here when user cancels.
     * GET /payment/cancel
     */
    public function cancel(Request $request)
    {
        return view('payment.cancel');
    }

    /**
     * Server-to-server callback from ABA PayWay confirming final transaction status.
     * POST /api/payment/webhook
     */
    public function webhook(Request $request)
    {
        // Verify the hash from ABA to confirm authenticity
        $apiKey   = config('payway.api_key');
        $reqTime  = $request->input('req_time');
        $merchant = $request->input('merchant_id');
        $tranId   = $request->input('tran_id');
        $amount   = $request->input('amount');
        $status   = $request->input('status');
        $received = $request->input('hash');

        $preHashStr    = $reqTime . $merchant . $tranId . $amount . $status;
        $expectedHash  = base64_encode(hash_hmac('sha512', $preHashStr, $apiKey, true));

        if (!hash_equals($expectedHash, (string) $received)) {
            return response()->json(['error' => 'Invalid hash'], 400);
        }

        // TODO: persist order/payment record here when you add a database model

        return response()->json(['status' => 'received']);
    }
}
