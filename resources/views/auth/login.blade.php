<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | {{ env('APP_NAME') }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('login_assets/images/icons/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('login_assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('login_assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(59,130,246,.28), transparent 35%),
                radial-gradient(circle at bottom right, rgba(14,165,233,.18), transparent 35%),
                linear-gradient(135deg, #020617, #0f172a);
            color: #e5e7eb;
            font-family: Arial, sans-serif;
        }

        .bd-login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .bd-login-card {
            width: 100%;
            max-width: 430px;
            background: rgba(17, 24, 39, .92);
            border: 1px solid rgba(148, 163, 184, .18);
            border-radius: 24px;
            box-shadow: 0 24px 70px rgba(0,0,0,.45);
            padding: 34px;
        }

        .bd-logo {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: rgba(59,130,246,.16);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 22px;
        }

        .bd-title {
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .bd-subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 26px;
        }

        .bd-label {
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .bd-input {
            width: 100%;
            background: #0f172a;
            border: 1px solid rgba(148, 163, 184, .24);
            color: #f8fafc;
            border-radius: 14px;
            padding: 13px 15px;
            min-height: 48px;
            outline: none;
        }

        .bd-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 .18rem rgba(59,130,246,.18);
        }

        .bd-error {
            color: #f87171;
            font-size: 12px;
            margin-top: 6px;
            display: block;
        }

        .bd-forgot {
            color: #93c5fd;
            font-size: 13px;
            text-decoration: none;
        }

        .bd-forgot:hover {
            color: #bfdbfe;
            text-decoration: none;
        }

        .bd-btn {
            width: 100%;
            min-height: 50px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            font-weight: 800;
            margin-top: 22px;
            transition: .15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .bd-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(37,99,235,.28);
        }

        .bd-btn:disabled {
            opacity: .75;
            cursor: not-allowed;
        }

        .loader {
            border: 3px solid rgba(255,255,255,.25);
            border-top: 3px solid #fff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>

<div class="bd-login-page">
    <div class="bd-login-card">

        <div class="bd-logo">
            <i class="fa fa-ship"></i>
        </div>

        <div class="bd-title">Welcome back</div>
        <div class="bd-subtitle">Sign in to your boat telemetry dashboard.</div>

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="bd-label">Email</label>
                <input
                    class="bd-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >

                @error('email')
                    <span class="bd-error"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="mb-2">
                <label class="bd-label">Password</label>
                <input
                    class="bd-input"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >

                @error('password')
                    <span class="bd-error"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a class="bd-forgot" href="{{ route('forget.password.get') }}">
                    Forgot your password?
                </a>
            </div>

            <button type="submit" class="bd-btn" id="loginBtn">
                <span id="btnText">Log in</span>
                <div class="loader" id="loader"></div>
            </button>
        </form>

    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function () {
        document.getElementById('loader').style.display = 'inline-block';
        document.getElementById('btnText').style.display = 'none';
        document.getElementById('loginBtn').disabled = true;
    });
</script>

</body>
</html>