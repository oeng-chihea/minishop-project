<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Result – Mini Shop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            border-radius: 4px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .icon { font-size: 56px; margin-bottom: 16px; }
        h1 { margin: 0 0 10px; font-size: 28px; }
        p  { color: #607080; margin: 6px 0; }
        .tran-id { font-size: 13px; color: #99aaba; margin-top: 12px; }
        .btn {
            display: inline-block;
            margin-top: 28px;
            padding: 12px 28px;
            background: #1f3a5f;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-radius: 2px;
        }
        .btn:hover { opacity: 0.88; }
    </style>
</head>
<body>
    <div class="card">
        @if ($status == '0')
            <div class="icon">✅</div>
            <h1>Payment Successful</h1>
            <p>Thank you! Your order has been confirmed.</p>
            @if ($amount)
                <p>Amount paid: <strong>${{ $amount }}</strong></p>
            @endif
        @else
            <div class="icon">❌</div>
            <h1>Payment Failed</h1>
            <p>Your payment could not be processed. Please try again.</p>
            <p>Status code: <strong>{{ $status }}</strong></p>
        @endif

        @if ($tranId)
            <p class="tran-id">Transaction ID: {{ $tranId }}</p>
        @endif

        <a href="{{ url('/') }}#shop" class="btn">Back to Shop</a>
    </div>
</body>
</html>
