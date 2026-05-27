<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name', 'URLC System') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f172a;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            width: 55%;
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            overflow: hidden;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #1a56a0 100%);
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            animation: pulse 6s ease-in-out infinite;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 179, 237, 0.1) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
            animation: pulse 8s ease-in-out infinite reverse;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .left-panel-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            max-width: 480px;
        }

        .system-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
            font-size: 2.5rem;
            color: white;
        }

        .system-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 40%, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .system-subtitle {
            font-size: 1rem;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        .feature-list {
            list-style: none;
            text-align: left;
            display: inline-block;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
            margin-bottom: 0.85rem;
        }

        .feature-list li .icon-check {
            width: 24px;
            height: 24px;
            background: rgba(59, 130, 246, 0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #60a5fa;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 45%;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 3.5rem;
            position: relative;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* ── ALERTS ── */
        .alert-custom {
            border-radius: 12px;
            font-size: 0.875rem;
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
            border: none;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .alert-success-custom {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        .alert-danger-custom {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* ── FORM ── */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label-custom {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            color: #111827;
            background: #f9fafb;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #3b82f6;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 1rem;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0;
        }

        .toggle-password:hover {
            color: #3b82f6;
        }

        /* ── REMEMBER & FORGOT ── */
        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #374151;
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #3b82f6;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #1d4ed8;
        }

        /* ── SUBMIT BUTTON ── */
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.35);
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 6px 28px rgba(59, 130, 246, 0.5);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ── REGISTER LINK ── */
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .register-link a {
            color: #3b82f6;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .register-link a:hover {
            color: #1d4ed8;
        }

        /* ── DIVIDER ── */
        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 1.5rem 0;
        }

        /* ── FOOTER ── */
        .login-footer {
            position: absolute;
            bottom: 1.5rem;
            font-size: 0.78rem;
            color: #cbd5e1;
            text-align: center;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; min-height: 260px; padding: 2rem; }
            .right-panel { width: 100%; padding: 2rem 1.5rem; }
            .system-title { font-size: 1.6rem; }
        }
    </style>
</head>
<body>

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="left-panel-content">
            <div class="system-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1 class="system-title">URLC Digital Platform</h1>
            <p class="system-subtitle">
                University Research Lifecycle System — A centralized platform for managing research proposals from submission to completion.
            </p>
            <ul class="feature-list">
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Role-Based Access Control for all users
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    5-Phase Research Lifecycle Management
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Automated Proposal Workflow and Status Tracking
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Secure Document Storage via Supabase S3
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Real-Time Notifications and Feedback
                </li>
            </ul>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="login-box">

            <div class="login-header">
                <h2>Welcome back</h2>
                <p>Sign in to your account to continue</p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="alert-custom alert-success-custom">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-custom alert-danger-custom">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-custom alert-danger-custom">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0; margin-top:2px;"></i>
                    <ul style="list-style:none; padding:0; margin:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label-custom" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input
                            id="email"
                            class="form-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            required
                            autofocus
                        />
                        <i class="bi bi-envelope input-icon"></i>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label-custom" for="password">Password</label>
                    <div class="input-wrapper">
                        <input
                            id="password"
                            class="form-input"
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                            style="padding-right: 3rem;"
                        />
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="toggle-password" onclick="togglePassword()" id="toggleBtn">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="form-extras">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember_me">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                </button>
            </form>

            <div class="divider"></div>

            <div class="register-link">
                Don't have an account?
                <a href="{{ route('register') }}">Create one here</a>
            </div>

        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} URLC Digital Platform. All rights reserved.
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Button loading state on submit
        document.querySelector('form').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Signing in...';
            btn.disabled = true;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
