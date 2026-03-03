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
        $lifetime     = (int) config('bakong.qr_lifetime', 300);

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

        // Expiration in milliseconds from now
        $expirationMs = (string) (intval(microtime(true) * 1000) + ($lifetime * 1000));

        try {
            $info = new IndividualInfo(
                $accountId,
                $merchantName,
                $merchantCity,
                null,           // acquiringBank
                null,           // accountInformation
                $currency,      // int: 840=USD, 116=KHR
                (float) $amount,
                $billNumber,
            );

            $khqrResponse = BakongKHQR::generateIndividual($info);

            $qrString = $khqrResponse->data['qr']  ?? null;
            $md5      = $khqrResponse->data['md5']  ?? null;

            if (!$qrString || !$md5) {
                Log::error('Bakong KHQR generation returned empty data', ['response' => $khqrResponse]);
                return response()->json(['message' => 'Failed to generate KHQR. Please try again.'], 500);
            }

            // Render QR string to a base64 PNG image
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
     * Poll transaction status by MD5 hash.
     * Called by Vue frontend via POST /api/bakong/check-status
     */
    public function checkStatus(Request $request)
    {
        $request->validate(['md5' => 'required|string|size:32']);

        $token  = (string) config('bakong.token');
        $isTest = config('bakong.environment') !== 'production';

        if ($token === '') {
            return response()->json(['message' => 'Bakong token is not configured.'], 500);
        }

        try {
            $bakong   = new BakongKHQR($token);
            $response = $bakong->checkTransactionByMD5($request->md5, $isTest);

            // Bakong returns null data when transaction is not yet completed
            $paid = isset($response['data']) && $response['data'] !== null;

            return response()->json([
                'paid'     => $paid,
                'response' => $response,
            ]);

        } catch (\Throwable $e) {
            Log::error('Bakong check-status error', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Could not check payment status. Please try again.'], 500);
        }
    }

    /**
     * Generate a base64-encoded PNG QR image from a KHQR string.
     */
    private function renderQrImage(string $data): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'scale'      => 6,
            'margin'     => 2,
            'eccLevel'   => QRCode::ECC_M,
        ]);

        return (new QRCode($options))->render($data);
    }
}
