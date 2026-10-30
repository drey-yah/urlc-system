<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'URLC System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- App CSS/JS -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script>
            (function() {
                try {
                    if (window.innerWidth >= 992 && localStorage.getItem('sidebar_state') === 'closed') {
                        document.documentElement.classList.add('sidebar-closed');
                    }
                } catch(e) {}

                // bfcache protection: reload page if navigated via back/forward history so server-side auth is enforced
                window.addEventListener('pageshow', function(event) {
                    var perfEntries = (window.performance && window.performance.getEntriesByType)
                        ? window.performance.getEntriesByType('navigation')
                        : [];
                    var isBackForward = (perfEntries.length > 0 && perfEntries[0].type === 'back_forward')
                        || (window.performance && window.performance.navigation && window.performance.navigation.type === 2);

                    if (event.persisted || isBackForward) {
                        window.location.reload();
                    }
                });
            })();
        </script>
        <style>
            /* Centered Flash Alert Pop-up Modal Styles */
            .popup-alert-modal .modal-content {
                border-radius: 24px !important;
                border: 1px solid rgba(0, 0, 0, 0.08);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
                animation: popupModalBounce 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            }
            @keyframes popupModalBounce {
                0% { transform: scale(0.85); opacity: 0; }
                60% { transform: scale(1.02); opacity: 1; }
                100% { transform: scale(1); opacity: 1; }
            }
            .popup-icon-pulse-success {
                animation: successPulse 2.2s infinite;
            }
            @keyframes successPulse {
                0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.45); }
                70% { box-shadow: 0 0 0 14px rgba(16, 185, 129, 0); }
                100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
            }
            .popup-icon-pulse-error {
                animation: errorPulse 2.2s infinite;
            }
            @keyframes errorPulse {
                0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.45); }
                70% { box-shadow: 0 0 0 14px rgba(239, 68, 68, 0); }
                100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
            }
            .modal-backdrop.show {
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
            }
        </style>
    </head>

    <body class="antialiased" style="font-family: 'Inter', sans-serif; background-color: #FAF7F5;">
        <!-- Mobile Top Navigation Bar (Visible on mobile/tablet screens) -->
        <header class="mobile-topbar d-lg-none sticky-top w-100 d-flex align-items-center justify-content-between px-3 py-2">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-link text-white p-1 text-decoration-none d-flex align-items-center" id="sidebarToggle" aria-label="Toggle navigation menu">
                    <i class="bi bi-list fs-1 text-white"></i>
                </button>
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('logo.png') }}" alt="Seal" class="rounded-circle" style="width: 32px; height: 32px; object-fit: contain;">
                    <div class="lh-1">
                        <span class="fw-bold text-white fs-6 d-block" style="font-family: 'Playfair Display', Georgia, serif;">URLC Portal</span>
                        <span class="text-gold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">University of Antique</span>
                    </div>
                </a>
            </div>
            <div class="d-flex align-items-center gap-2">
                @auth
                    @include('layouts.partials.role_switcher', ['isMobile' => true])

                    @include('layouts.partials.notification_bell', ['isMobile' => true])

                    <a href="{{ route('messages.index') }}" class="btn btn-link text-white p-1 text-decoration-none position-relative" title="Messages">
                        <i class="bi bi-envelope-fill fs-5" style="color: var(--ua-gold);"></i>
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 text-decoration-none ms-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Account">
                            <span class="rounded-circle fw-bold text-dark d-inline-flex align-items-center justify-content-center shadow-xs" 
                                  style="width: 32px; height: 32px; background: var(--ua-gold); font-size: 0.85rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-2" style="min-width: 190px;">
                            <li class="px-3 py-1.5 border-bottom">
                                <div class="fw-bold text-dark small text-truncate">{{ auth()->user()->name }}</div>
                                <div class="text-muted text-capitalize" style="font-size: 0.72rem;">{{ str_replace('_', ' ', auth()->user()->activeRole()) }}</div>
                            </li>
                            <li><a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('profile.show') }}"><i class="bi bi-person text-muted"></i> Profile</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item small py-2 text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </header>

        <!-- Sidebar Backdrop for Mobile Screens -->
        <div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop"></div>

        <div class="d-flex w-100" style="min-width: 0;">
            @include('layouts.navigation')

            <div class="main-content flex-grow-1" style="min-width: 0;">
                <!-- Desktop Global Topbar (Upper Right Notification Bell & Controls) -->
                @auth
                <header class="d-none d-lg-flex justify-content-between align-items-center mb-4 pb-2 border-bottom" style="border-color: rgba(140, 29, 44, 0.08) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Desktop Sidebar Open/Close Toggle Button -->
                        <button type="button" 
                                class="btn btn-white bg-white border rounded-circle shadow-xs d-inline-flex align-items-center justify-content-center p-0 notif-bell-btn me-1" 
                                id="desktopSidebarToggle" 
                                title="Open / Close Sidebar" 
                                style="width: 40px; height: 40px; color: #4b5563;">
                            <i class="bi bi-list fs-4 text-dark"></i>
                        </button>

                        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 1.15rem; letter-spacing: -0.01em;">
                            Welcome back, <span style="color: var(--ua-maroon, #8C1D2C);">{{ auth()->user()->name }}</span> 👋
                        </h4>
                    </div>

                    <div class="d-flex align-items-center gap-2.5">
                        <!-- Role Switcher for Multi-Role Accounts -->
                        @include('layouts.partials.role_switcher', ['isMobile' => false])

                        <a href="{{ route('messages.index') }}" class="btn btn-white bg-white border rounded-circle shadow-xs position-relative d-inline-flex align-items-center justify-content-center p-0 notif-bell-btn" style="width: 40px; height: 40px; color: #4b5563;" title="Messages">
                            <i class="bi bi-envelope fs-5" style="color: #6b7280;"></i>
                        </a>

                        <!-- Upper Right Corner Notification Bell Button -->
                        @include('layouts.partials.notification_bell', ['isMobile' => false])

                        <!-- User Profile Pill Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-white bg-white border rounded-pill shadow-xs d-flex align-items-center gap-2 py-1 px-2.5" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="rounded-circle text-white fw-bold d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 28px; height: 28px; background: var(--ua-maroon, #8C1D2C); font-size: 0.78rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="small fw-semibold text-dark text-truncate" style="max-width: 140px;">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down text-muted" style="font-size: 0.7rem;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-2" style="min-width: 200px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold text-dark small">{{ auth()->user()->name }}</div>
                                    <div class="text-muted text-capitalize" style="font-size: 0.74rem;">{{ str_replace('_', ' ', auth()->user()->activeRole()) }}</div>
                                </li>
                                <li><a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('profile.show') }}"><i class="bi bi-person text-muted"></i> Profile</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item small py-2 text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>
                @endauth

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm mb-4 rounded-4 p-4 border">
                        {{ $header }}
                    </header>
                @endisset

                <!-- Page Content -->
                <div class="py-4">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Centered Flash Alert Pop-up Modals (Success / Error / Status) -->
        @if(session('success'))
            <div class="modal fade popup-alert-modal" id="flashSuccessModal" tabindex="-1" aria-labelledby="flashSuccessModalLabel" aria-hidden="true" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
                    <div class="modal-content border-0 shadow-2xl rounded-4 text-center p-4 position-relative overflow-hidden" style="background: #ffffff;">
                        <!-- Top Green Gradient Accent Bar -->
                        <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(90deg, #10b981 0%, #34d399 100%);"></div>
                        
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.82rem;"></button>

                        <div class="pt-2 pb-1">
                            <!-- Pulsing Green Checkmark -->
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle popup-icon-pulse-success" 
                                 style="width: 76px; height: 76px; background: #ecfdf5; border: 2px solid #a7f3d0; color: #059669;">
                                <i class="bi bi-check-lg" style="font-size: 2.6rem;"></i>
                            </div>

                            <h5 class="modal-title fw-bold text-dark mb-2" id="flashSuccessModalLabel" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                                Success!
                            </h5>

                            <p class="text-secondary mb-4 px-2" style="font-size: 0.96rem; line-height: 1.55; color: #4b5563;">
                                {{ session('success') }}
                            </p>

                            <div class="d-grid gap-2">
                                <button type="button" 
                                        class="btn fw-semibold py-2.5 rounded-pill shadow-sm text-white" 
                                        id="flashSuccessDismissBtn" 
                                        data-bs-dismiss="modal" 
                                        autofocus
                                        style="background: linear-gradient(135deg, #8C1D2C 0%, #A52A3A 100%); font-size: 0.95rem; border: none;">
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="modal fade popup-alert-modal" id="flashErrorModal" tabindex="-1" aria-labelledby="flashErrorModalLabel" aria-hidden="true" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
                    <div class="modal-content border-0 shadow-2xl rounded-4 text-center p-4 position-relative overflow-hidden" style="background: #ffffff;">
                        <!-- Top Red Gradient Accent Bar -->
                        <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);"></div>
                        
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.82rem;"></button>

                        <div class="pt-2 pb-1">
                            <!-- Pulsing Red Triangle Icon -->
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle popup-icon-pulse-error" 
                                 style="width: 76px; height: 76px; background: #fef2f2; border: 2px solid #fecaca; color: #dc2626;">
                                <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.3rem;"></i>
                            </div>

                            <h5 class="modal-title fw-bold text-dark mb-2" id="flashErrorModalLabel" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                                Attention Needed
                            </h5>

                            <p class="text-secondary mb-4 px-2" style="font-size: 0.96rem; line-height: 1.55; color: #4b5563;">
                                {{ session('error') }}
                            </p>

                            <div class="d-grid gap-2">
                                <button type="button" 
                                        class="btn btn-secondary fw-semibold py-2.5 rounded-pill shadow-sm" 
                                        id="flashErrorDismissBtn" 
                                        data-bs-dismiss="modal" 
                                        autofocus
                                        style="font-size: 0.95rem;">
                                    Understood
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('status') && !session('success'))
            <div class="modal fade popup-alert-modal" id="flashStatusModal" tabindex="-1" aria-labelledby="flashStatusModalLabel" aria-hidden="true" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
                    <div class="modal-content border-0 shadow-2xl rounded-4 text-center p-4 position-relative overflow-hidden" style="background: #ffffff;">
                        <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);"></div>
                        
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.82rem;"></button>

                        <div class="pt-2 pb-1">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 76px; height: 76px; background: #eff6ff; border: 2px solid #bfdbfe; color: #2563eb;">
                                <i class="bi bi-info-circle-fill" style="font-size: 2.3rem;"></i>
                            </div>

                            <h5 class="modal-title fw-bold text-dark mb-2" id="flashStatusModalLabel" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                                Notice
                            </h5>

                            <p class="text-secondary mb-4 px-2" style="font-size: 0.96rem; line-height: 1.55; color: #4b5563;">
                                {{ session('status') }}
                            </p>

                            <div class="d-grid gap-2">
                                <button type="button" 
                                        class="btn btn-primary fw-semibold py-2.5 rounded-pill shadow-sm" 
                                        data-bs-dismiss="modal" 
                                        autofocus
                                        style="background: var(--ua-maroon, #8C1D2C); border-color: var(--ua-maroon, #8C1D2C); font-size: 0.95rem;">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Navigation Drawer & Sidebar Collapse Handler -->
        <script>
            // ========================================================
            // CENTERED FLASH ALERT POP-UP BOX AUTO-TRIGGER
            // ========================================================
            function triggerFlashModals() {
                const successModalEl = document.getElementById('flashSuccessModal');
                if (successModalEl && window.bootstrap && window.bootstrap.Modal) {
                    const successModal = bootstrap.Modal.getOrCreateInstance(successModalEl);
                    successModal.show();

                    successModalEl.addEventListener('shown.bs.modal', function () {
                        const btn = document.getElementById('flashSuccessDismissBtn');
                        if (btn) btn.focus();
                    });
                }

                const errorModalEl = document.getElementById('flashErrorModal');
                if (errorModalEl && window.bootstrap && window.bootstrap.Modal) {
                    const errorModal = bootstrap.Modal.getOrCreateInstance(errorModalEl);
                    errorModal.show();

                    errorModalEl.addEventListener('shown.bs.modal', function () {
                        const btn = document.getElementById('flashErrorDismissBtn');
                        if (btn) btn.focus();
                    });
                }

                const statusModalEl = document.getElementById('flashStatusModal');
                if (statusModalEl && window.bootstrap && window.bootstrap.Modal) {
                    const statusModal = bootstrap.Modal.getOrCreateInstance(statusModalEl);
                    statusModal.show();
                }
            }

            // Execute immediately if DOM is already ready, or on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', triggerFlashModals);
            } else {
                triggerFlashModals();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.getElementById('sidebarNav') || document.querySelector('.sidebar');
                const toggleBtn = document.getElementById('sidebarToggle');
                const desktopToggleBtn = document.getElementById('desktopSidebarToggle');
                const closeBtn = document.getElementById('sidebarCloseBtn');
                const backdrop = document.getElementById('sidebarBackdrop');

                // Synchronize body class from documentElement on load
                if (document.documentElement.classList.contains('sidebar-closed')) {
                    document.body.classList.add('sidebar-closed');
                }

                function isDesktop() {
                    return window.innerWidth >= 992;
                }

                function toggleDesktopSidebar() {
                    const isClosed = document.body.classList.toggle('sidebar-closed');
                    if (isClosed) {
                        document.documentElement.classList.add('sidebar-closed');
                        localStorage.setItem('sidebar_state', 'closed');
                    } else {
                        document.documentElement.classList.remove('sidebar-closed');
                        localStorage.setItem('sidebar_state', 'open');
                    }
                }

                function openMobileSidebar() {
                    if (sidebar) sidebar.classList.add('show');
                    if (backdrop) backdrop.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }

                function closeMobileSidebar() {
                    if (sidebar) sidebar.classList.remove('show');
                    if (backdrop) backdrop.classList.remove('show');
                    document.body.style.overflow = '';
                }

                if (desktopToggleBtn) {
                    desktopToggleBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (isDesktop()) {
                            toggleDesktopSidebar();
                        } else {
                            openMobileSidebar();
                        }
                    });
                }

                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openMobileSidebar();
                    });
                }

                if (closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (isDesktop()) {
                            toggleDesktopSidebar();
                        } else {
                            closeMobileSidebar();
                        }
                    });
                }

                if (backdrop) {
                    backdrop.addEventListener('click', closeMobileSidebar);
                }

                // Close on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        if (!isDesktop() && sidebar && sidebar.classList.contains('show')) {
                            closeMobileSidebar();
                        }
                    }
                });

                // Auto-close drawer when clicking on sidebar links on mobile
                if (sidebar) {
                    const links = sidebar.querySelectorAll('.sidebar-link');
                    links.forEach(function(link) {
                        link.addEventListener('click', function() {
                            if (!isDesktop()) {
                                closeMobileSidebar();
                            }
                        });
                    });
                }

                // ========================================================
                // GLOBAL DOUBLE-SUBMIT & MULTI-CLICK FLOODING PROTECTION
                // ========================================================
                document.addEventListener('submit', function (e) {
                    const form = e.target;
                    if (!form || form.tagName !== 'FORM') return;

                    // If user cancelled confirmation or HTML5 validation failed, don't lock form
                    if (e.defaultPrevented) return;

                    // Prevent duplicate submission if form is already in-flight
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Find the submit button
                    const submitBtn = e.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        form.dataset.submitting = 'true';

                        const originalHtml = submitBtn.innerHTML;
                        const originalWidth = submitBtn.offsetWidth;

                        // Give small delay so browser registers form-data and dispatches request
                        setTimeout(() => {
                            if (submitBtn && form.dataset.submitting === 'true') {
                                if (originalWidth) {
                                    submitBtn.style.minWidth = originalWidth + 'px';
                                }
                                submitBtn.classList.add('disabled', 'opacity-75');
                                submitBtn.style.pointerEvents = 'none';
                                submitBtn.innerHTML = `
                                    <span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true" style="width: 0.9rem; height: 0.9rem;"></span>
                                    <span>Processing...</span>
                                `;
                            }
                        }, 50);

                        // Safety timeout: re-enable after 8 seconds in case validation fails or connection stalls
                        setTimeout(() => {
                            if (submitBtn) {
                                submitBtn.classList.remove('disabled', 'opacity-75');
                                submitBtn.style.pointerEvents = '';
                                submitBtn.style.minWidth = '';
                                submitBtn.innerHTML = originalHtml;
                            }
                            form.dataset.submitting = 'false';
                        }, 8000);
                    }
                });
            });
        </script>
    </body>
</html>