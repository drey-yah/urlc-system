<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register — {{ config('app.name', 'URLC System') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f172a;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            width: 42%;
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
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
            top: -80px;
            right: -80px;
            animation: pulse 6s ease-in-out infinite;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 179, 237, 0.1) 0%, transparent 70%);
            bottom: -60px;
            left: -60px;
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
            max-width: 420px;
        }

        .system-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
            font-size: 2.2rem;
            color: white;
        }

        .system-title {
            font-size: 2rem;
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
            font-size: 0.95rem;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .step-list {
            list-style: none;
            text-align: left;
            display: inline-block;
        }

        .step-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: rgba(255,255,255,0.75);
            font-size: 0.875rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .step-num {
            width: 24px;
            height: 24px;
            background: rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #93c5fd;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 58%;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2.5rem 3.5rem;
            position: relative;
            overflow-y: auto;
        }

        .register-box {
            width: 100%;
            max-width: 560px;
            padding: 1rem 0;
        }

        .register-header {
            margin-bottom: 2rem;
        }

        .register-header h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }

        .register-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* ── ALERT ── */
        .alert-custom {
            border-radius: 12px;
            font-size: 0.875rem;
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
            border: none;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
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
            color: #374151;
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
            color: #9ca3af;
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.925rem;
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

        .input-wrapper:focus-within .input-icon {
            color: #3b82f6;
        }

        .form-select-custom {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.925rem;
            font-family: 'Inter', sans-serif;
            color: #111827;
            background: #f9fafb;
            transition: all 0.2s ease;
            outline: none;
            appearance: none;
            cursor: pointer;
        }

        .form-select-custom:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
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

        .toggle-password:hover { color: #3b82f6; }

        .form-hint {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* ── ROLE BADGE ── */
        .role-notice {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.8rem;
            color: #1e40af;
            display: none;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        /* ── SUBMIT ── */
        .btn-register {
            width: 100%;
            padding: 0.875rem;
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
            margin-top: 0.5rem;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 6px 28px rgba(59, 130, 246, 0.5);
            transform: translateY(-1px);
        }

        .btn-register:active { transform: translateY(0); }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 1.25rem 0;
        }

        .login-link {
            text-align: center;
            font-size: 0.9rem;
            color: #64748b;
        }

        .login-link a {
            color: #3b82f6;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-link a:hover { color: #1d4ed8; }

        .login-footer {
            position: absolute;
            bottom: 1.25rem;
            font-size: 0.78rem;
            color: #cbd5e1;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; min-height: 240px; padding: 2rem; }
            .right-panel { width: 100%; padding: 2rem 1.5rem; }
            .form-row { grid-template-columns: 1fr; }
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
            <h1 class="system-title">Join the URLC Platform</h1>
            <p class="system-subtitle">
                Create your account and become part of the University Research Lifecycle community.
            </p>
            <ul class="step-list">
                <li>
                    <span class="step-num">1</span>
                    Fill in your personal details and choose your role in the research community.
                </li>
                <li>
                    <span class="step-num">2</span>
                    Some roles such as Admin and Support Staff require Super Admin approval before access is granted.
                </li>
                <li>
                    <span class="step-num">3</span>
                    Once approved, log in and access your personalized role-based dashboard.
                </li>
            </ul>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="register-box">

            <div class="register-header">
                <h2>Create your account</h2>
                <p>Fill in the details below to get started</p>
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

                <!-- Name & Email -->
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
                        <label class="form-label-custom" for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input id="email" class="form-input" type="email" name="email"
                                value="{{ old('email') }}" placeholder="you@example.com"
                                required />
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Password & Confirm -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label-custom" for="password">Password</label>
                        <div class="input-wrapper">
                            <input id="password" class="form-input" type="password" name="password"
                                placeholder="Create a password" required autocomplete="new-password"
                                style="padding-right:3rem;" />
                            <i class="bi bi-lock input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePass('password','eye1')">
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
                            <button type="button" class="toggle-password" onclick="togglePass('password_confirmation','eye2')">
                                <i class="bi bi-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- College / Department -->
                <div class="form-group">
                    <label class="form-label-custom" for="department">College / Department</label>
                    <div class="input-wrapper">
                        <select id="department" name="department" class="form-select-custom">
                            <option value="">— Not Applicable —</option>
                            <option value="CCIS" {{ old('department') == 'CCIS' ? 'selected' : '' }}>College of Computing and Information Sciences (CCIS)</option>
                            <option value="CAS"  {{ old('department') == 'CAS'  ? 'selected' : '' }}>College of Art and Sciences (CAS)</option>
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
                            <option value="staff"           {{ old('role') == 'staff'            ? 'selected' : '' }}>Support Staff (Receiving)</option>
                            <option value="recording_staff" {{ old('role') == 'recording_staff'  ? 'selected' : '' }}>Support Staff (Recording)</option>
                            <option value="admin"           {{ old('role') == 'admin'            ? 'selected' : '' }}>Administrator</option>
                        </select>
                        <i class="bi bi-shield-check input-icon"></i>
                    </div>
                    <div class="role-notice" id="approvalNotice">
                        <i class="bi bi-hourglass-split"></i>
                        This role requires <strong>&nbsp;Super Admin approval&nbsp;</strong> before you can access the system.
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="bi bi-person-plus-fill me-2"></i>Create Account
                </button>
            </form>

            <div class="divider"></div>

            <div class="login-link">
                Already have an account?
                <a href="{{ route('login') }}">Sign in here</a>
            </div>

        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} URLC Digital Platform. All rights reserved.
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
        const approvalRoles = ['staff', 'recording_staff', 'admin', 'dean', 'vprei'];
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
