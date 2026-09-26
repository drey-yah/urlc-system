<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — URLC Research Portal | University of Antique</title>

    <!-- Google Fonts: Inter & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --ua-maroon-dark: #120305;
            --ua-maroon-bg: #1a0508;
            --ua-maroon-panel: #24080e;
            --ua-maroon-card: rgba(38, 12, 17, 0.72);
            --ua-maroon-border: rgba(212, 175, 55, 0.22);
            --ua-gold-primary: #E5A93C;
            --ua-gold-light: #FDE047;
            --ua-gold-accent: #D4AF37;
            --ua-text-main: #FFFFFF;
            --ua-text-muted: #D6CBC4;
            --ua-crimson: #8C1D2C;
            --ua-crimson-hover: #A32234;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background-color: var(--ua-maroon-dark);
            color: var(--ua-text-main);
        }

        /* ── LEFT PANEL (BRANDING) ── */
        .left-panel {
            width: 52%;
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3.5rem 3rem;
            overflow: hidden;
            background: radial-gradient(circle at 25% 20%, #4a101a 0%, #20060b 55%, #100204 100%);
            border-right: 1px solid rgba(212, 175, 55, 0.18);
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 550px;
            height: 550px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(229, 169, 60, 0.12) 0%, transparent 70%);
            top: -120px;
            left: -80px;
            animation: pulse-glow 7s ease-in-out infinite;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(140, 29, 44, 0.22) 0%, transparent 70%);
            bottom: -100px;
            right: -80px;
            animation: pulse-glow 9s ease-in-out infinite reverse;
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .left-panel-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            max-width: 470px;
        }

        .seal-wrapper {
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .ua-seal-img {
            width: 105px;
            height: 105px;
            border-radius: 50%;
            object-fit: contain;
            filter: drop-shadow(0 6px 20px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 16px rgba(229, 169, 60, 0.35));
            transition: transform 0.3s ease;
        }

        .ua-seal-img:hover {
            transform: scale(1.05);
        }

        .inst-sublabel {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--ua-gold-primary);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        .system-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.35rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 0.85rem;
            color: #FFFFFF;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .system-subtitle {
            font-size: 0.95rem;
            color: var(--ua-text-muted);
            line-height: 1.65;
            margin-bottom: 2.25rem;
        }

        .feature-list {
            list-style: none;
            text-align: left;
            display: inline-block;
            margin: 0;
            padding: 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #E2D9D0;
            font-size: 0.88rem;
            margin-bottom: 0.85rem;
        }

        .feature-list li .icon-check {
            width: 22px;
            height: 22px;
            background: rgba(229, 169, 60, 0.18);
            border: 1px solid rgba(229, 169, 60, 0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ua-gold-primary);
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        /* ── RIGHT PANEL (LOGIN FORM) ── */
        .right-panel {
            width: 48%;
            min-height: 100vh;
            background-color: #170407;
            background-image: radial-gradient(circle at 80% 80%, #29080f 0%, #170407 60%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 3rem;
            position: relative;
        }

        .back-nav {
            position: absolute;
            top: 1.5rem;
            left: 2rem;
        }

        .back-link {
            color: var(--ua-text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .back-link:hover {
            color: var(--ua-gold-primary);
            border-color: rgba(229, 169, 60, 0.3);
            background: rgba(229, 169, 60, 0.08);
            transform: translateX(-2px);
        }

        .login-box {
            width: 100%;
            max-width: 410px;
        }

        .login-header {
            margin-bottom: 2.25rem;
        }

        .login-header h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2rem;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 0.35rem;
        }

        .login-header p {
            color: var(--ua-text-muted);
            font-size: 0.92rem;
        }

        /* ── ALERTS ── */
        .alert-custom {
            border-radius: 10px;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
        }

        .alert-success-custom {
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        /* ── FORM FIELDS ── */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label-custom {
            display: block;
            font-size: 0.86rem;
            font-weight: 600;
            color: #F5ECE5;
            margin-bottom: 0.45rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #8C7E77;
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            border: 1.5px solid rgba(212, 175, 55, 0.22);
            border-radius: 10px;
            font-size: 0.93rem;
            font-family: 'Inter', sans-serif;
            color: #FFFFFF;
            background: rgba(36, 10, 15, 0.65);
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: #7D6F6A;
        }

        .form-input:focus {
            border-color: var(--ua-gold-primary);
            background: rgba(45, 12, 18, 0.9);
            box-shadow: 0 0 0 3px rgba(229, 169, 60, 0.2);
            color: #FFFFFF;
        }

        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--ua-gold-primary);
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8C7E77;
            font-size: 1rem;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0;
        }

        .toggle-password:hover {
            color: var(--ua-gold-primary);
        }

        /* ── REMEMBER & FORGOT ── */
        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.6rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--ua-text-muted);
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--ua-crimson);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.85rem;
            color: var(--ua-gold-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--ua-gold-light);
            text-decoration: underline;
        }

        /* ── SUBMIT BUTTON ── */
        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #8C1D2C 0%, #5E0E1B 100%);
            color: #FFFFFF;
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 10px;
            font-size: 0.98rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            letter-spacing: 0.2px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #A32234 0%, #731221 100%);
            border-color: var(--ua-gold-primary);
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.5), 0 0 16px rgba(229, 169, 60, 0.25);
            transform: translateY(-1px);
            color: #FFFFFF;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ── DIVIDER & REGISTER LINK ── */
        .divider {
            height: 1px;
            background: rgba(212, 175, 55, 0.15);
            margin: 1.6rem 0;
        }

        .register-link {
            text-align: center;
            font-size: 0.88rem;
            color: var(--ua-text-muted);
        }

        .register-link a {
            color: var(--ua-gold-primary);
            font-weight: 600;
            text-decoration: none;
            margin-left: 0.3rem;
            transition: color 0.2s;
        }

        .register-link a:hover {
            color: var(--ua-gold-light);
            text-decoration: underline;
        }

        /* ── FOOTER ── */
        .login-footer {
            position: absolute;
            bottom: 1.5rem;
            font-size: 0.78rem;
            color: #7D6F6A;
            text-align: center;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }
            .left-panel {
                width: 100%;
                min-height: auto;
                padding: 2.75rem 1.5rem 2rem;
                border-right: none;
                border-bottom: 1px solid rgba(212, 175, 55, 0.18);
            }
            .right-panel {
                width: 100%;
                min-height: auto;
                padding: 3rem 1.5rem;
            }
            .back-nav {
                top: 1rem;
                left: 1rem;
            }
            .system-title {
                font-size: 1.85rem;
            }
            .feature-list {
                display: none;
            }
            .system-subtitle {
                margin-bottom: 0;
            }
            .login-footer {
                position: relative;
                bottom: auto;
                margin-top: 2.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- LEFT PANEL: UNIVERSITY OF ANTIQUE BRANDING -->
    <div class="left-panel">
        <div class="left-panel-content">
            <div class="seal-wrapper">
                <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" class="ua-seal-img">
            </div>
            <div class="inst-sublabel">University of Antique</div>
            <h1 class="system-title">URLC Digital Platform</h1>
            <p class="system-subtitle">
                University Research Lifecycle System — A centralized institutional portal for managing academic proposals from submission to completion.
            </p>
            <ul class="feature-list">
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Role-Based Access Control for all 9 university offices
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    5-Phase Research Lifecycle & Compliance Workflow
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Automated Endorsement and NTP Approval Tracking
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Secure Cloud Storage with Pre-Signed Document Links
                </li>
                <li>
                    <span class="icon-check"><i class="bi bi-check-lg"></i></span>
                    Real-Time Reviewer Feedback & Evaluation Matrices
                </li>
            </ul>
        </div>
    </div>

    <!-- RIGHT PANEL: SIGN IN FORM -->
    <div class="right-panel">
        <!-- Return to Home / Portal -->
        <div class="back-nav">
            <a href="{{ url('/') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Return to Portal
            </a>
        </div>

        <div class="login-box">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in with your institutional credentials to continue</p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="alert-custom alert-success-custom">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-custom alert-danger-custom">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>{{ session('error') }}</div>
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

                <!-- Email Address -->
                <div class="form-group">
                    <label class="form-label-custom" for="email">Institutional Email</label>
                    <div class="input-wrapper">
                        <input
                            id="email"
                            class="form-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="username@antiquespride.edu.ph"
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
                            placeholder="Enter your account password"
                            required
                            autocomplete="current-password"
                            style="padding-right: 3rem;"
                        />
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="toggle-password" onclick="togglePassword()" id="toggleBtn" aria-label="Toggle password visibility">
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

                <!-- Submit Button -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Portal
                </button>
            </form>

            <div class="divider"></div>

            <div class="register-link">
                Don't have an account?
                <a href="{{ route('register') }}">Create an account</a>
            </div>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} University of Antique • URLC Digital Platform
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
