<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Your Email — URLC Research Portal | University of Antique</title>

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
            --ua-maroon-card: rgba(38, 12, 17, 0.85);
            --ua-maroon-border: rgba(212, 175, 55, 0.28);
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
            align-items: center;
            justify-content: center;
            background-color: var(--ua-maroon-dark);
            color: var(--ua-text-main);
            position: relative;
            overflow-x: hidden;
            padding: 2rem 1rem;
        }

        /* Ambient Glow Background */
        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(229, 169, 60, 0.12) 0%, transparent 70%);
            top: -150px;
            left: -100px;
            z-index: 0;
            animation: pulse-glow 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(140, 29, 44, 0.25) 0%, transparent 70%);
            bottom: -150px;
            right: -100px;
            z-index: 0;
            animation: pulse-glow 10s ease-in-out infinite reverse;
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .verify-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 580px;
            background: var(--ua-maroon-card);
            border: 1px solid var(--ua-maroon-border);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 30px rgba(229, 169, 60, 0.15);
            text-align: center;
        }

        .seal-wrapper {
            margin-bottom: 1.25rem;
            display: inline-block;
        }

        .ua-seal-img {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            object-fit: contain;
            filter: drop-shadow(0 6px 18px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 14px rgba(229, 169, 60, 0.35));
            transition: transform 0.3s ease;
        }

        .ua-seal-img:hover {
            transform: scale(1.05);
        }

        .inst-sublabel {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--ua-gold-primary);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .verify-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.85rem;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 0.75rem;
        }

        .email-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(229, 169, 60, 0.12);
            border: 1px solid rgba(229, 169, 60, 0.3);
            color: var(--ua-gold-light);
            padding: 0.45rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            word-break: break-all;
        }

        .verify-desc {
            color: var(--ua-text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        .alert-sent {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--ua-gold-primary) 0%, var(--ua-gold-accent) 100%);
            color: #120305;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.85rem 1.75rem;
            border: none;
            border-radius: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(229, 169, 60, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #f0b953 0%, var(--ua-gold-primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 169, 60, 0.45);
            color: #000;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--ua-text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            text-decoration: none;
        }

        .btn-logout:hover {
            border-color: rgba(255, 255, 255, 0.4);
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.05);
        }

        .tips-card {
            background: rgba(0, 0, 0, 0.25);
            border: 1px dashed rgba(212, 175, 55, 0.25);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-top: 1.75rem;
            text-align: left;
            font-size: 0.82rem;
            color: var(--ua-text-muted);
            line-height: 1.5;
        }

        .tips-card i {
            color: var(--ua-gold-primary);
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <!-- University Seal -->
        <div class="seal-wrapper">
            <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" class="ua-seal-img">
        </div>

        <div class="inst-sublabel">University of Antique &bull; Research & Development Services</div>
        <h1 class="verify-title">Verify Your Institutional Email</h1>

        <!-- Recipient Email Pill -->
        <div class="email-badge">
            <i class="bi bi-envelope-at-fill"></i>
            <span>{{ auth()->user()->email ?? 'your institutional email' }}</span>
        </div>

        <!-- Description -->
        <p class="verify-desc">
            Thanks for creating your account on the <strong>URLC Digital Platform</strong>! 
            To activate full access to research proposal submissions and institutional evaluations, please click the verification link we just emailed to you.
        </p>

        <!-- Flash Alert when Link is Resent -->
        @if (session('status') == 'verification-link-sent')
            <div class="alert-sent">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>
                    A new verification link has been sent to your email address. Please check your inbox and spam folder.
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="d-flex flex-column gap-2">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-send-fill"></i> Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Sign Out &amp; Use Another Account
                </button>
            </form>
        </div>

        <!-- Help & Tips Box -->
        <div class="tips-card">
            <div class="fw-semibold text-white mb-1">
                <i class="bi bi-info-circle me-1"></i> Didn't receive the email?
            </div>
            <div>
                &bull; Allow up to 1&ndash;2 minutes for delivery to complete.<br>
                &bull; Check your <strong>Spam</strong> or <strong>Junk</strong> folder.<br>
                &bull; Click <strong>Resend Verification Email</strong> above if the link expired.
            </div>
        </div>
    </div>

</body>
</html>
