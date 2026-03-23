<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

            // Persist order as PENDING — DB errors are non-fatal so QR always renders.
            $orderNumber = null;
            try {
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'bill_number'  => $billNumber,
                    'md5'          => $md5,
                    'status'       => 'pending',
                    'total_amount' => $amount,
                    'currency'     => $currencyStr,
                ]);

                foreach ($request->items as $item) {
                    $qty   = (int)   $item['qty'];
                    $price = (float) $item['price'];
                    $order->items()->create([
                        'product_id'    => (int) ($item['id'] ?? 0),
                        'product_name'  => $item['name'],
                        'unit_price'    => $price,
                        'quantity'      => $qty,
                        'subtotal'      => round($price * $qty, 2),
                        'product_image' => $item['image'] ?? null,
                    ]);
                }

                $orderNumber = $order->order_number;

                Log::info('Bakong KHQR generated', [
                    'order_number' => $orderNumber,
                    'md5'          => $md5,
                    'amount'       => $amount,
                    'currency'     => $currencyStr,
                    'bill_number'  => $billNumber,
                ]);
            } catch (\Throwable $dbErr) {
                // DB unavailable — log and continue; QR is still valid.
                Log::warning('Bakong order DB save failed (non-fatal)', [
                    'bill_number' => $billNumber,
                    'error'       => $dbErr->getMessage(),
                ]);
                // Fallback: generate display-only order number
                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
            }

            return response()->json([
                'qr_string'    => $qrString,
                'qr_image'     => $qrImage,
                'md5'          => $md5,
                'amount'       => $amount,
                'currency'     => $currencyStr,
                'bill_number'  => $billNumber,
                'order_number' => $orderNumber,
                'bakong_token' => config('bakong.token'), // browser polls Bakong directly
            ]);

        } catch (\Throwable $e) {
            Log::error('Bakong KHQR initiate error', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Could not generate Bakong QR. Please try again.'], 500);
        }
    }

    /**
     * Server-side Bakong transaction poll — proxies the MD5 check to Bakong API.
     * Uses stream_context (not cURL) to try a different network path.
     * Called by the Vue frontend every few seconds after checkout.
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'md5'        => 'nullable|string',
            'billNumber' => 'nullable|string',
        ]);

        $token  = (string) config('bakong.token');
        $isTest = config('bakong.environment') !== 'production';
        $base   = $isTest
            ? 'https://sit-api-bakong.nbc.gov.kh'
            : 'https://api-bakong.nbc.gov.kh';

        if ($token === '') {
            return response()->json(['message' => 'Bakong token is not configured.'], 500);
        }

        $md5        = (string) ($request->input('md5', ''));
        $billNumber = (string) ($request->input('billNumber', ''));

        // Try MD5 check
        if ($md5 !== '') {
            $result = $this->bakongPost("$base/v1/check_transaction_by_md5", ['md5' => $md5], $token);
            if ($result !== null) {
                $paid = isset($result['data']) && $result['data'] !== null;
                return response()->json(['paid' => $paid, 'raw' => $result]);
            }
        }

        // Fallback: instructionRef check
        if ($billNumber !== '') {
            $result = $this->bakongPost("$base/v1/check_transaction_by_instruction_ref", ['instructionRef' => $billNumber], $token);
            if ($result !== null) {
                $paid = isset($result['data']) && $result['data'] !== null;
                return response()->json(['paid' => $paid, 'raw' => $result]);
            }
        }

        // Both methods failed — server cannot reach Bakong API
        return response()->json(['paid' => false, 'error' => 'unreachable']);
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
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9,km;q=0.8',
                'Authorization: Bearer ' . $token,
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Origin: https://api-bakong.nbc.gov.kh',
                'Referer: https://api-bakong.nbc.gov.kh/',
                'sec-ch-ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
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
     * Mark an order as paid — called silently by the frontend after customer confirms.
     * Non-fatal: if DB is unavailable the response is still 200.
     */
    public function confirmOrder(string $orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)
                          ->where('status', 'pending')
                          ->first();
            if ($order) {
                $order->update(['status' => 'paid', 'paid_at' => now()]);
            }
        } catch (\Throwable $e) {
            Log::warning('Order confirm failed (non-fatal)', [
                'order_number' => $orderNumber,
                'error'        => $e->getMessage(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Temporary diagnostic endpoint - shows exactly why live server can't reach Bakong API.
     * Access via: GET /api/bakong/diagnose
     * Remove after debugging is done.
     */
    public function diagnose()
    {
        $token = (string) config('bakong.token');
        $url   = 'https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5';
        $results = [];

        // Test 1: basic connectivity
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['md5' => 'test']),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9,km;q=0.8',
                'Authorization: Bearer ' . $token,
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Origin: https://api-bakong.nbc.gov.kh',
                'Referer: https://api-bakong.nbc.gov.kh/',
                'sec-ch-ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
            ],
        ]);
        $body    = curl_exec($ch);
        $code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        $curlErrNo = curl_errno($ch);
        curl_close($ch);

        $results['bakong_api'] = [
            'http_code'    => $code,
            'curl_error'   => $curlErr,
            'curl_errno'   => $curlErrNo,
            'body_preview' => substr((string) $body, 0, 500),
        ];

        // Test 2: DNS resolution
        $ip = gethostbyname('api-bakong.nbc.gov.kh');
        $results['dns'] = [
            'resolved_ip' => $ip,
            'dns_works'   => ($ip !== 'api-bakong.nbc.gov.kh'),
        ];

        // Test 3: server info + real public IP
        $publicIp = 'unknown';
        $ipCh = curl_init('https://api.ipify.org');
        curl_setopt_array($ipCh, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
        $publicIp = trim((string) curl_exec($ipCh));
        curl_close($ipCh);

        $results['server'] = [
            'php_version'     => PHP_VERSION,
            'server_internal_ip' => $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname()),
            'server_public_ip'   => $publicIp,   // ← THIS is what Bakong sees
            'curl_version'    => curl_version()['version'],
            'openssl_version' => curl_version()['ssl_version'],
        ];

        return response()->json($results);
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
