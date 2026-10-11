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
                    <a href="{{ route('messages.index') }}" class="btn btn-link text-white p-1 text-decoration-none position-relative" title="Messages">
                        <i class="bi bi-envelope-fill fs-5" style="color: var(--ua-gold);"></i>
                    </a>
                    <a href="{{ route('profile.show') }}" class="text-decoration-none ms-1" title="My Profile">
                        <span class="rounded-circle fw-bold text-dark d-inline-flex align-items-center justify-content-center shadow-xs" 
                              style="width: 32px; height: 32px; background: var(--ua-gold); font-size: 0.85rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </a>
                @endauth
            </div>
        </header>

        <!-- Sidebar Backdrop for Mobile Screens -->
        <div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop"></div>

        <div class="d-flex w-100" style="min-width: 0;">
            @include('layouts.navigation')

            <div class="main-content flex-grow-1" style="min-width: 0;">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm mb-4 rounded-4 p-4 border">
                        {{ $header }}
                    </header>
                @endisset

                <!-- Page Content -->
                <div class="py-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Mobile Navigation Drawer Handler -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.getElementById('sidebarNav') || document.querySelector('.sidebar');
                const toggleBtn = document.getElementById('sidebarToggle');
                const closeBtn = document.getElementById('sidebarCloseBtn');
                const backdrop = document.getElementById('sidebarBackdrop');

                function openSidebar() {
                    if (sidebar) sidebar.classList.add('show');
                    if (backdrop) backdrop.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }

                function closeSidebar() {
                    if (sidebar) sidebar.classList.remove('show');
                    if (backdrop) backdrop.classList.remove('show');
                    document.body.style.overflow = '';
                }

                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openSidebar();
                    });
                }

                if (closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeSidebar();
                    });
                }

                if (backdrop) {
                    backdrop.addEventListener('click', closeSidebar);
                }

                // Close on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
                        closeSidebar();
                    }
                });

                // Auto-close drawer when clicking on sidebar links (for smooth page transition)
                if (sidebar) {
                    const links = sidebar.querySelectorAll('.sidebar-link');
                    links.forEach(function(link) {
                        link.addEventListener('click', function() {
                            if (window.innerWidth < 992) {
                                closeSidebar();
                            }
                        });
                    });
                }
            });
        </script>
    </body>
</html>