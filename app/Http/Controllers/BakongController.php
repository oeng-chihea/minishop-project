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
     * Poll transaction status.
     * Called by Vue frontend via POST /api/bakong/check-status
     *
     * Tries MD5 first (works when Bakong indexes the exact QR hash).
     * Falls back to instructionRef (billNumber) which Bakong records from
     * the bill number embedded in the Static QR's tag-62 additional data.
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'md5'         => 'required|string|size:32',
            'bill_number' => 'required|string|max:25',
        ]);

        $token  = (string) config('bakong.token');
        $isTest = config('bakong.environment') !== 'production';

        if ($token === '') {
            return response()->json(['message' => 'Bakong token is not configured.'], 500);
        }

        $baseUrl = $isTest
            ? 'https://sit-api-bakong.nbc.gov.kh'
            : 'https://api-bakong.nbc.gov.kh';

        // --- Try MD5 ---
        $response = $this->bakongPost("{$baseUrl}/v1/check_transaction_by_md5", ['md5' => $request->md5], $token);

        if ($response !== null) {
            $paid = isset($response['data']) && $response['data'] !== null;
            Log::error('Bakong check-status (md5)', ['md5' => $request->md5, 'paid' => $paid, 'response' => $response]);
            if ($paid) {
                return response()->json(['paid' => true, 'response' => $response]);
            }
        } else {
            Log::error('Bakong MD5 check returned null (request failed)', ['md5' => $request->md5]);
        }

        // --- Fallback: instructionRef (billNumber) ---
        $response = $this->bakongPost("{$baseUrl}/v1/check_transaction_by_instruction_ref", ['instructionRef' => $request->bill_number], $token);

        if ($response !== null) {
            $paid = isset($response['data']) && $response['data'] !== null;
            Log::error('Bakong check-status (instructionRef)', ['bill_number' => $request->bill_number, 'paid' => $paid, 'response' => $response]);
            return response()->json(['paid' => $paid, 'response' => $response]);
        }

        Log::error('Bakong all checks failed', ['md5' => $request->md5, 'bill_number' => $request->bill_number]);
        return response()->json(['paid' => false]);
    }

    /**
     * POST to a Bakong API endpoint with a browser-like User-Agent.
     * The library's default cURL has no User-Agent which some WAFs block with 403.
     *
     * @return array<string,mixed>|null  null on network/HTTP failure
     */
    private function bakongPost(string $url, array $payload, string $token): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token,
                'User-Agent: Mozilla/5.0 (compatible; BakongKHQR/1.0)',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $httpCode !== 200) {
            Log::error('bakongPost failed', ['url' => $url, 'http' => $httpCode, 'curl_error' => $curlErr, 'body' => substr((string) $body, 0, 300)]);
            return null;
        }

        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : null;
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
