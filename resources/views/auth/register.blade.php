<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register — URLC Research Portal | University of Antique</title>

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

        /* ── LEFT PANEL (BRANDING & STEPS) ── */
        .left-panel {
            width: 40%;
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3.5rem 2.5rem;
            overflow: hidden;
            background: radial-gradient(circle at 20% 25%, #4a101a 0%, #20060b 55%, #100204 100%);
            border-right: 1px solid rgba(212, 175, 55, 0.18);
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(229, 169, 60, 0.12) 0%, transparent 70%);
            top: -100px;
            left: -80px;
            animation: pulse-glow 7s ease-in-out infinite;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(140, 29, 44, 0.22) 0%, transparent 70%);
            bottom: -80px;
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
            max-width: 420px;
        }

        .seal-wrapper {
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .ua-seal-img {
            width: 100px;
            height: 100px;
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
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 0.85rem;
            color: #FFFFFF;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .system-subtitle {
            font-size: 0.92rem;
            color: var(--ua-text-muted);
            line-height: 1.6;
            margin-bottom: 2.25rem;
        }

        .step-list {
            list-style: none;
            text-align: left;
            display: inline-block;
            margin: 0;
            padding: 0;
        }

        .step-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            color: #E2D9D0;
            font-size: 0.875rem;
            margin-bottom: 1.15rem;
            line-height: 1.5;
        }

        .step-num {
            width: 26px;
            height: 26px;
            background: rgba(229, 169, 60, 0.18);
            border: 1px solid rgba(229, 169, 60, 0.45);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ua-gold-primary);
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── RIGHT PANEL (REGISTRATION FORM) ── */
        .right-panel {
            width: 60%;
            min-height: 100vh;
            background-color: #170407;
            background-image: radial-gradient(circle at 85% 85%, #29080f 0%, #170407 60%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 3.5rem;
            position: relative;
            overflow-y: auto;
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

        .register-box {
            width: 100%;
            max-width: 580px;
            padding: 1.5rem 0;
        }

        .register-header {
            margin-bottom: 2rem;
            margin-top: 1rem;
        }

        .register-header h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2rem;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 0.35rem;
        }

        .register-header p {
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
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        /* ── FORM GRID ── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-label-custom {
            display: block;
            font-size: 0.85rem;
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

        .form-input, .form-select-custom {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1.5px solid rgba(212, 175, 55, 0.22);
            border-radius: 10px;
            font-size: 0.92rem;
            font-family: 'Inter', sans-serif;
            color: #FFFFFF;
            background: rgba(36, 10, 15, 0.65);
            transition: all 0.2s ease;
            outline: none;
        }

        .form-select-custom {
            appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23E5A93C' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 14px;
            padding-right: 2.5rem;
        }

        .form-select-custom option {
            background-color: #24080e;
            color: #FFFFFF;
        }

        .form-input::placeholder {
            color: #7D6F6A;
        }

        .form-input:focus, .form-select-custom:focus {
            border-color: var(--ua-gold-primary);
            background-color: rgba(45, 12, 18, 0.9);
            box-shadow: 0 0 0 3px rgba(229, 169, 60, 0.2);
            color: #FFFFFF;
        }

        .form-input:focus + .input-icon,
        .form-select-custom:focus + .input-icon,
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

        .form-hint {
            font-size: 0.76rem;
            color: #9C8E87;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* ── ROLE NOTICE ── */
        .role-notice {
            background: rgba(229, 169, 60, 0.12);
            border: 1px solid rgba(229, 169, 60, 0.35);
            border-radius: 10px;
            padding: 0.7rem 0.95rem;
            font-size: 0.82rem;
            color: var(--ua-gold-light);
            display: none;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.65rem;
        }

        /* ── SUBMIT BUTTON ── */
        .btn-register {
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
            margin-top: 0.75rem;
            letter-spacing: 0.2px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #A32234 0%, #731221 100%);
            border-color: var(--ua-gold-primary);
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.5), 0 0 16px rgba(229, 169, 60, 0.25);
            transform: translateY(-1px);
            color: #FFFFFF;
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* ── DIVIDER & LOGIN LINK ── */
        .divider {
            height: 1px;
            background: rgba(212, 175, 55, 0.15);
            margin: 1.5rem 0;
        }

        .login-link {
            text-align: center;
            font-size: 0.88rem;
            color: var(--ua-text-muted);
        }

        .login-link a {
            color: var(--ua-gold-primary);
            font-weight: 600;
            text-decoration: none;
            margin-left: 0.3rem;
            transition: color 0.2s;
        }

        .login-link a:hover {
            color: var(--ua-gold-light);
            text-decoration: underline;
        }

        /* ── FOOTER ── */
        .register-footer {
            margin-top: 2rem;
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
            .form-row {
                grid-template-columns: 1fr;
            }
            .system-title {
                font-size: 1.85rem;
            }
            .step-list {
                display: none;
            }
            .system-subtitle {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>

    <!-- LEFT PANEL: UNIVERSITY OF ANTIQUE BRANDING & STEPS -->
    <div class="left-panel">
        <div class="left-panel-content">
            <div class="seal-wrapper">
                <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" class="ua-seal-img">
            </div>
            <div class="inst-sublabel">University of Antique</div>
            <h1 class="system-title">Join the URLC Platform</h1>
            <p class="system-subtitle">
                Create your institutional account and become part of the University of Antique research community.
            </p>
            <ul class="step-list">
                <li>
                    <span class="step-num">1</span>
                    <div>Fill in your personal details and choose your role in the academic workflow.</div>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <div>Executive & administrative roles require Super Admin verification before access is granted.</div>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <div>Once verified, sign in to access your customized role-based research workspace.</div>
                </li>
            </ul>
        </div>
    </div>

    <!-- RIGHT PANEL: REGISTRATION FORM -->
    <div class="right-panel">
        <!-- Return to Home / Portal -->
        <div class="back-nav">
            <a href="{{ url('/') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Return to Portal
            </a>
        </div>

        <div class="register-box">
            <div class="register-header">
                <h2>Create Your Account</h2>
                <p>Fill in the details below to register for institutional access</p>
            </div>

            @if ($errors->any())
                <div class="alert-custom">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0; margin-top:2px;"></i>
                    <ul style="list-style:none; padding:0; margin:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name & Email (2-Column Grid) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label-custom" for="name">Full Name</label>
                        <div class="input-wrapper">
                            <input id="name" class="form-input" type="text" name="name"
                                value="{{ old('name') }}" placeholder="Juan Dela Cruz"
                                required autofocus />
                            <i class="bi bi-person input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom" for="email">Institutional Email</label>
                        <div class="input-wrapper">
                            <input id="email" class="form-input" type="email" name="email"
                                value="{{ old('email') }}" placeholder="username@antiquespride.edu.ph"
                                required />
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Password & Confirm (2-Column Grid) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label-custom" for="password">Password</label>
                        <div class="input-wrapper">
                            <input id="password" class="form-input" type="password" name="password"
                                placeholder="Create a secure password" required autocomplete="new-password"
                                style="padding-right:3rem;" />
                            <i class="bi bi-lock input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePass('password','eye1')" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="eye1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom" for="password_confirmation">Confirm Password</label>
                        <div class="input-wrapper">
                            <input id="password_confirmation" class="form-input" type="password"
                                name="password_confirmation" placeholder="Repeat your password"
                                required style="padding-right:3rem;" />
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePass('password_confirmation','eye2')" aria-label="Toggle confirm password visibility">
                                <i class="bi bi-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Organization / Funding Agency -->
                <div class="form-group">
                    <label class="form-label-custom" for="organization">Organization / Funding Agency</label>
                    <div class="input-wrapper">
                        <input id="organization" class="form-input" type="text" name="organization"
                            value="{{ old('organization') }}" placeholder="Name of your organization (Optional)" />
                        <i class="bi bi-buildings input-icon"></i>
                    </div>
                    <div class="form-hint"><i class="bi bi-info-circle"></i> Required only when registering as a Funding Agency.</div>
                </div>

                <!-- College / Department -->
                <div class="form-group">
                    <label class="form-label-custom" for="department">College / Department</label>
                    <div class="input-wrapper">
                        <select id="department" name="department" class="form-select-custom">
                            <option value="">— Not Applicable —</option>
                            <option value="CCIS" {{ old('department') == 'CCIS' ? 'selected' : '' }}>College of Computing and Information Sciences (CCIS)</option>
                            <option value="CAS"  {{ old('department') == 'CAS'  ? 'selected' : '' }}>College of Arts and Sciences (CAS)</option>
                            <option value="CIT"  {{ old('department') == 'CIT'  ? 'selected' : '' }}>College of Industrial Technology (CIT)</option>
                            <option value="CMS"  {{ old('department') == 'CMS'  ? 'selected' : '' }}>College of Maritime Studies (CMS)</option>
                            <option value="CCJE" {{ old('department') == 'CCJE' ? 'selected' : '' }}>College of Criminal Justice Education (CCJE)</option>
                            <option value="CMG"  {{ old('department') == 'CMG'  ? 'selected' : '' }}>College of Management and Governance (CMG)</option>
                            <option value="CTE"  {{ old('department') == 'CTE'  ? 'selected' : '' }}>College of Teacher Education (CTE)</option>
                        </select>
                        <i class="bi bi-building input-icon"></i>
                    </div>
                    <div class="form-hint">
                        <i class="bi bi-info-circle"></i>
                        Required for Researchers and Coordinators.
                    </div>
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label class="form-label-custom" for="role">Register as</label>
                    <div class="input-wrapper">
                        <select id="role" name="role" class="form-select-custom" required onchange="checkRole(this)">
                            <option value="researcher"      {{ old('role') == 'researcher'       ? 'selected' : '' }}>Researcher</option>
                            <option value="reviewer"        {{ old('role') == 'reviewer'         ? 'selected' : '' }}>Reviewer / Evaluator</option>
                            <option value="coordinator"     {{ old('role') == 'coordinator'      ? 'selected' : '' }}>College Coordinator</option>
                            <option value="dean"            {{ old('role') == 'dean'             ? 'selected' : '' }}>College Dean</option>
                            <option value="vprei"           {{ old('role') == 'vprei'            ? 'selected' : '' }}>VP for Research (VPREI)</option>
                            <option value="president"       {{ old('role') == 'president'        ? 'selected' : '' }}>SUC President</option>
                            <option value="staff"           {{ old('role') == 'staff'            ? 'selected' : '' }}>Support Staff (Receiving)</option>
                            <option value="recording_staff" {{ old('role') == 'recording_staff'  ? 'selected' : '' }}>Support Staff (Recording)</option>
                            <option value="budget_officer"  {{ old('role') == 'budget_officer'   ? 'selected' : '' }}>Budget Officer</option>
                            <option value="sao_finance"     {{ old('role') == 'sao_finance'      ? 'selected' : '' }}>Finance Officer</option>
                            <option value="funding_agency"  {{ old('role') == 'funding_agency'   ? 'selected' : '' }}>Funding Agency</option>
                            <option value="admin"           {{ old('role') == 'admin'            ? 'selected' : '' }}>Administrator</option>
                        </select>
                        <i class="bi bi-shield-check input-icon"></i>
                    </div>
                    <div class="role-notice" id="approvalNotice">
                        <i class="bi bi-hourglass-split"></i>
                        <span>This role requires <strong>Super Admin approval</strong> before system access is granted.</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="bi bi-person-plus-fill me-2"></i>Create Account
                </button>
            </form>

            <div class="divider"></div>

            <div class="login-link">
                Already have an account?
                <a href="{{ route('login') }}">Sign in here</a>
            </div>

            <div class="register-footer">
                &copy; {{ date('Y') }} University of Antique • URLC Digital Platform
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePass(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Show approval notice for roles that require it
        const approvalRoles = ['staff', 'recording_staff', 'admin', 'dean', 'vprei', 'budget_officer', 'sao_finance', 'president', 'funding_agency'];
        function checkRole(select) {
            const notice = document.getElementById('approvalNotice');
            notice.style.display = approvalRoles.includes(select.value) ? 'flex' : 'none';
        }

        // Trigger on page load for old() value
        checkRole(document.getElementById('role'));

        // Loading state on submit
        document.querySelector('form').addEventListener('submit', function () {
            const btn = document.getElementById('registerBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Creating account...';
            btn.disabled = true;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
