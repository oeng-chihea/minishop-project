<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — minishopkh</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
            background: #060b14;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* subtle grid texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
        }

        .card {
            position: relative;
            background: #0d1526;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 48px 44px 44px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
        }

        .lock-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.25);
            margin-bottom: 28px;
            color: #3b82f6;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #fff;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 13px;
            color: rgba(255,255,255,0.38);
            margin-bottom: 32px;
            line-height: 1.5;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.38);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 24px;
        }

        input[type="password"] {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 7px;
            padding: 13px 44px 13px 16px;
            color: #fff;
            font-size: 15px;
            font-family: inherit;
            letter-spacing: 0.1em;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        input[type="password"]:focus {
            border-color: rgba(59, 130, 246, 0.6);
            background: rgba(59, 130, 246, 0.05);
        }

        input[type="password"].has-error {
            border-color: rgba(239,68,68,0.6);
            background: rgba(239,68,68,0.05);
        }

        /* toggle show/hide */
        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255,255,255,0.3);
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .eye-btn:hover { color: rgba(255,255,255,0.7); }

        .error-msg {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13px;
            color: #fca5a5;
            margin-bottom: 20px;
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s, transform 0.15s;
        }
        .submit-btn:hover  { background: #2563eb; }
        .submit-btn:active { transform: scale(0.98); }

        .footer-note {
            margin-top: 24px;
            text-align: center;
            font-size: 11.5px;
            color: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- Lock icon -->
        <div class="lock-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>

        <h1>Admin Access</h1>
        <p class="subtitle">Enter your passcode to access the dashboard.</p>

        @if ($errors->any())
        <div class="error-msg">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $errors->first('passcode') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <label for="passcode">Passcode</label>
            <div class="input-wrap">
                <input
                    type="password"
                    id="passcode"
                    name="passcode"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    class="{{ $errors->any() ? 'has-error' : '' }}"
                    autofocus
                />
                <button type="button" class="eye-btn" onclick="togglePass(this)" aria-label="Toggle passcode visibility">
                    <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>

            <button type="submit" class="submit-btn">Enter Dashboard</button>
        </form>

        <p class="footer-note">minishopkh · Admin Panel</p>
    </div>

    <script>
        function togglePass(btn) {
            const input = document.getElementById('passcode');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.querySelector('svg').style.opacity = isPass ? '0.8' : '0.3';
        }
    </script>
</body>
</html>
