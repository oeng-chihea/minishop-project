<?php

namespace App\Http\Controllers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class BakongController extends Controller
{
    /**
     * Generate a Bakong KHQR code for the cart items.
     * Called by Vue frontend via POST /api/bakong/checkout
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.name'    => 'required|string',
            'items.*.qty'     => 'required|integer|min:1',
            'items.*.price'   => 'required|numeric|min:0',
        ]);

        $accountId    = (string) config('bakong.account_id');
        $merchantName = (string) config('bakong.merchant_name');
        $merchantCity = (string) config('bakong.merchant_city');
        $currencyStr  = strtoupper((string) config('bakong.currency', 'USD'));

        if ($accountId === '') {
            return response()->json(['message' => 'Bakong account ID is not configured.'], 500);
        }

        // Calculate total amount from cart items
        $amount = collect($request->items)
            ->reduce(fn ($carry, $item) => $carry + ((float) $item['price'] * (int) $item['qty']), 0.0);
        $amount = round($amount, 2);

        $currency = $currencyStr === 'KHR' ? KHQRData::CURRENCY_KHR : KHQRData::CURRENCY_USD;

        // Build a bill number — alphanumeric only, max 25 chars (KHQR spec)
        $billNumber = 'INV' . now()->format('YmdHis');

        try {
            // Static QR (amount=0, code 11) — required because Dynamic QR (code 12) needs
            // to be registered with Bakong's Deep Link API first; without that, bank apps
            // (ABA, Wing, etc.) reject it with MAPP-KHQR-INV-FORMAT. Static QR is validated
            // locally by the bank app so it always scans fine. Payment is detected via
            // checkTransactionByExternalReference(billNumber) instead of MD5 — this works
            // for Static QR because the billNumber is embedded in the QR and Bakong records
            // it as the external reference when the payer confirms the transaction.
            $info = new IndividualInfo(
                $accountId,
                $merchantName,
                $merchantCity,
                null,           // acquiringBank
                null,           // accountInformation
                $currency,      // int: 840=USD, 116=KHR
                0.0,            // amount=0 → Static QR (code 11); no Deep Link registration needed
                $billNumber,    // unique per checkout; used as externalRef for payment detection
            );

            $khqrResponse = BakongKHQR::generateIndividual($info);

            $qrString = $khqrResponse->data['qr']  ?? null;
            $md5      = $khqrResponse->data['md5']  ?? null;

            if (!$qrString || !$md5) {
                Log::error('Bakong KHQR generation returned empty data', ['response' => $khqrResponse]);
                return response()->json(['message' => 'Failed to generate KHQR. Please try again.'], 500);
            }

            $qrImage = $this->renderQrImage($qrString);

            Log::info('Bakong KHQR generated', [
                'qr_string'   => $qrString,
                'md5'         => $md5,
                'amount'      => $amount,
                'currency'    => $currencyStr,
                'account_id'  => $accountId,
                'bill_number' => $billNumber,
            ]);

            return response()->json([
                'qr_string' => $qrString,
                'qr_image'  => $qrImage,   // "data:image/png;base64,..."
                'md5'       => $md5,
                'amount'    => $amount,
                'currency'  => $currencyStr,
                'bill_number' => $billNumber,
            ]);

        } catch (\Throwable $e) {
            Log::error('Bakong KHQR initiate error', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Could not generate Bakong QR. Please try again.'], 500);
        }
    }

    /**
     * Poll transaction status by bill number (external reference).
     * Called by Vue frontend via POST /api/bakong/check-status
     *
     * Uses checkTransactionByExternalReference instead of checkTransactionByMD5
     * because Static QR (code 11) payments are tracked by bill number in Bakong's
     * production API. MD5 tracking only works for Dynamic QR (code 12) which requires
     * Deep Link API registration and is rejected by bank apps without it.
     */
    public function checkStatus(Request $request)
    {
        $request->validate(['bill_number' => 'required|string|max:25']);

        $token  = (string) config('bakong.token');
        $isTest = config('bakong.environment') !== 'production';

        if ($token === '') {
            return response()->json(['message' => 'Bakong token is not configured.'], 500);
        }

        try {
            $bakong   = new BakongKHQR($token);
            $response = $bakong->checkTransactionByExternalReference($request->bill_number, $isTest);

            // Bakong returns null data when transaction is not yet completed
            $paid = isset($response['data']) && $response['data'] !== null;

            return response()->json([
                'paid'     => $paid,
                'response' => $response,
            ]);

        } catch (\Throwable $e) {
            Log::warning('Bakong check-status error', ['message' => $e->getMessage(), 'bill_number' => $request->bill_number]);
            return response()->json(['paid' => false]);
        }
    }

    /**
     * Generate a base64-encoded PNG QR image from a KHQR string.
     */
    private function renderQrImage(string $data): string
    {
        $options = new QROptions([
            'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
            'outputBase64' => false,
            'scale'        => 6,
            'margin'       => 2,
            'eccLevel'     => QRCode::ECC_M,
        ]);

        return (new QRCode($options))->render($data);
    }
}
