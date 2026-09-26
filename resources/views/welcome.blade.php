<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>URLC Research Portal — University of Antique</title>

        <!-- Google Fonts: Inter & Playfair Display -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 & Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <style>
            :root {
                --ua-maroon-dark: #120305;
                --ua-maroon-bg: #1a0508;
                --ua-maroon-card: rgba(38, 12, 17, 0.72);
                --ua-maroon-card-hover: rgba(58, 18, 26, 0.95);
                --ua-maroon-border: rgba(212, 175, 55, 0.22);
                --ua-maroon-border-hover: rgba(234, 179, 8, 0.6);
                --ua-gold-primary: #E5A93C;
                --ua-gold-light: #FDE047;
                --ua-gold-accent: #D4AF37;
                --ua-text-main: #FFFFFF;
                --ua-text-muted: #D6CBC4;
                --ua-crimson: #8C1D2C;
            }

            * {
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background-color: var(--ua-maroon-dark);
                background-image: 
                    radial-gradient(circle at 50% 0%, #3d0e16 0%, #1a0508 45%, #100204 100%);
                background-attachment: fixed;
                color: var(--ua-text-main);
                min-height: 100vh;
                margin: 0;
                display: flex;
                flex-direction: column;
            }

            /* ── TOP NAV BAR ── */
            .top-navbar {
                background-color: #2b080e;
                border-bottom: 1px solid rgba(212, 175, 55, 0.18);
                padding: 0.65rem 1.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: sticky;
                top: 0;
                z-index: 100;
                backdrop-filter: blur(10px);
            }

            .brand-indicator {
                display: inline-flex;
                align-items: center;
                gap: 0.6rem;
                font-size: 0.9rem;
                font-weight: 600;
                color: #FFF;
                letter-spacing: 0.02em;
            }

            .brand-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: #EF4444;
                box-shadow: 0 0 8px #EF4444;
            }

            .guest-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.25rem 0.9rem;
                border-radius: 9999px;
                border: 1px solid rgba(212, 175, 55, 0.4);
                background: rgba(255, 255, 255, 0.04);
                color: var(--ua-gold-primary);
                font-size: 0.8rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .guest-pill:hover {
                background: rgba(212, 175, 55, 0.15);
                color: #FFF;
                border-color: var(--ua-gold-primary);
            }

            /* ── MAIN CONTENT CONTAINER ── */
            .main-content {
                flex: 1;
                max-width: 960px;
                width: 100%;
                margin: 0 auto;
                padding: 2.5rem 1.25rem 3.5rem;
            }

            /* ── HERO BANNER ── */
            .hero-section {
                text-align: center;
                margin-bottom: 3rem;
            }

            .seal-container {
                display: inline-block;
                position: relative;
                margin-bottom: 1.25rem;
            }

            .ua-seal {
                width: 96px;
                height: 96px;
                border-radius: 50%;
                object-fit: contain;
                filter: drop-shadow(0 6px 20px rgba(0, 0, 0, 0.6)) drop-shadow(0 0 12px rgba(229, 169, 60, 0.35));
                transition: transform 0.3s ease;
            }

            .ua-seal:hover {
                transform: scale(1.04);
            }

            .office-title {
                font-size: 0.88rem;
                font-weight: 600;
                color: var(--ua-gold-primary);
                letter-spacing: 0.03em;
                margin-bottom: 0.5rem;
                text-transform: capitalize;
            }

            .portal-heading {
                font-family: 'Playfair Display', Georgia, serif;
                font-size: 2.65rem;
                font-weight: 800;
                color: #FFFFFF;
                margin-bottom: 0.75rem;
                letter-spacing: -0.01em;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            }

            .portal-subtitle {
                font-size: 0.98rem;
                color: var(--ua-text-muted);
                max-width: 620px;
                margin: 0 auto 1.5rem;
                line-height: 1.55;
                font-weight: 400;
            }

            .gold-divider {
                width: 72px;
                height: 3px;
                border-radius: 999px;
                background: linear-gradient(90deg, transparent, var(--ua-gold-primary), transparent);
                margin: 0 auto;
            }

            /* ── CATEGORY GROUP ── */
            .category-section {
                margin-bottom: 2.25rem;
            }

            .category-header {
                margin-bottom: 0.85rem;
            }

            .category-title {
                font-size: 1.05rem;
                font-weight: 700;
                color: var(--ua-gold-primary);
                margin-bottom: 0.15rem;
                letter-spacing: 0.01em;
            }

            .category-desc {
                font-size: 0.83rem;
                color: #A39690;
                margin-bottom: 0;
            }

            /* ── ROLE GRID & CARDS ── */
            .roles-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            @media (min-width: 768px) {
                .roles-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0.85rem;
                }

                .roles-grid.single-item-grid {
                    grid-template-columns: 1fr;
                }

                .roles-grid .span-full {
                    grid-column: span 2;
                }
            }

            .role-card-item {
                display: flex;
                align-items: center;
                gap: 1rem;
                background: var(--ua-maroon-card);
                border: 1px solid var(--ua-maroon-border);
                border-radius: 12px;
                padding: 0.85rem 1.2rem;
                text-decoration: none;
                color: var(--ua-text-main);
                transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
                backdrop-filter: blur(8px);
                position: relative;
                overflow: hidden;
            }

            .role-card-item::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(90deg, rgba(229, 169, 60, 0.06), transparent);
                opacity: 0;
                transition: opacity 0.25s ease;
            }

            .role-card-item:hover {
                background: var(--ua-maroon-card-hover);
                border-color: var(--ua-maroon-border-hover);
                transform: translateY(-2px);
                box-shadow: 0 10px 24px -6px rgba(0, 0, 0, 0.5), 0 0 16px rgba(229, 169, 60, 0.15);
                color: var(--ua-text-main);
            }

            .role-card-item:hover::before {
                opacity: 1;
            }

            /* ── AVATAR BADGE ── */
            .role-badge {
                width: 42px;
                height: 42px;
                min-width: 42px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 1.05rem;
                letter-spacing: -0.02em;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            }

            .badge-crimson {
                background-color: var(--ua-crimson);
                color: #FFFFFF;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }

            .badge-gold {
                background-color: #C28D2B;
                color: #1A0709;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .badge-white {
                background-color: #F8FAFC;
                color: #3B0811;
                border: 1px solid rgba(0, 0, 0, 0.1);
            }

            /* ── ROLE DETAILS ── */
            .role-info {
                flex: 1;
                min-width: 0;
            }

            .role-name {
                font-size: 0.98rem;
                font-weight: 700;
                color: #FFFFFF;
                margin-bottom: 0.15rem;
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            .role-subtitle {
                font-size: 0.81rem;
                color: var(--ua-text-muted);
                margin: 0;
                line-height: 1.35;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            @media (max-width: 576px) {
                .role-subtitle {
                    white-space: normal;
                }
            }

            .chevron-icon {
                font-size: 0.85rem;
                color: rgba(229, 169, 60, 0.5);
                transition: transform 0.2s ease, color 0.2s ease;
            }

            .role-card-item:hover .chevron-icon {
                transform: translateX(4px);
                color: var(--ua-gold-primary);
            }

            /* ── REGISTER CTA ── */
            .register-section {
                text-align: center;
                margin-top: 3.5rem;
                padding-top: 2rem;
                border-top: 1px solid rgba(212, 175, 55, 0.15);
            }

            .register-text {
                font-size: 0.92rem;
                color: var(--ua-text-muted);
                margin-bottom: 1rem;
            }

            .btn-register-ua {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                background: linear-gradient(135deg, #7D1825 0%, #540D17 100%);
                color: #FFFFFF;
                font-weight: 600;
                font-size: 0.95rem;
                padding: 0.75rem 2.25rem;
                border-radius: 10px;
                border: 1px solid rgba(212, 175, 55, 0.35);
                text-decoration: none;
                transition: all 0.25s ease;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
            }

            .btn-register-ua:hover {
                background: linear-gradient(135deg, #961E2D 0%, #68111D 100%);
                border-color: var(--ua-gold-primary);
                color: #FFFFFF;
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5), 0 0 15px rgba(229, 169, 60, 0.2);
            }

            /* ── FOOTER ── */
            .portal-footer {
                text-align: center;
                padding: 1.5rem;
                font-size: 0.8rem;
                color: #8C7E77;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
            }

            .portal-footer a {
                color: var(--ua-gold-primary);
                text-decoration: none;
            }

            .portal-footer a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <!-- Top University Header Bar -->
        <header class="top-navbar">
            <div class="brand-indicator">
                <span class="brand-dot"></span>
                <span>University of Antique</span>
            </div>
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="guest-pill">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="guest-pill">
                        <i class="bi bi-box-arrow-in-right"></i> Guest
                    </a>
                @endauth
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Hero Header -->
            <section class="hero-section">
                <div class="seal-container">
                    <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" class="ua-seal">
                </div>
                <div class="office-title">
                    Office of the Vice President for Research, Extension & Innovation
                </div>
                <h1 class="portal-heading">
                    URLC Research Portal
                </h1>
                <p class="portal-subtitle">
                    Submit, endorse, and track research proposals from first draft to executive clearance — in one place.
                </p>
                <div class="gold-divider"></div>
            </section>

            <!-- Categorized Role Workspaces -->
            <div class="role-categories">
                <!-- 1. Research & review -->
                <section class="category-section">
                    <div class="category-header">
                        <h2 class="category-title">Research & review</h2>
                        <p class="category-desc">Write proposals and evaluate their merit.</p>
                    </div>
                    <div class="roles-grid">
                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-crimson">R</div>
                            <div class="role-info">
                                <div class="role-name">Researcher</div>
                                <p class="role-subtitle">Submit and track your research proposals</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>

                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-crimson">R</div>
                            <div class="role-info">
                                <div class="role-name">Reviewer</div>
                                <p class="role-subtitle">Evaluate proposals and give recommendations</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>
                    </div>
                </section>

                <!-- 2. Endorsement -->
                <section class="category-section">
                    <div class="category-header">
                        <h2 class="category-title">Endorsement</h2>
                        <p class="category-desc">Confirm proposals before they move up the chain.</p>
                    </div>
                    <div class="roles-grid">
                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-gold">C</div>
                            <div class="role-info">
                                <div class="role-name">Coordinator</div>
                                <p class="role-subtitle">Endorse proposals from your department</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>

                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-gold">D</div>
                            <div class="role-info">
                                <div class="role-name">College Dean</div>
                                <p class="role-subtitle">Review and note proposals from your college</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>
                    </div>
                </section>

                <!-- 3. Finance & compliance -->
                <section class="category-section">
                    <div class="category-header">
                        <h2 class="category-title">Finance & compliance</h2>
                        <p class="category-desc">Check budgets and route funds.</p>
                    </div>
                    <div class="roles-grid">
                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-crimson">S</div>
                            <div class="role-info">
                                <div class="role-name">Support Staff</div>
                                <p class="role-subtitle">Receive proposals and verify compliance</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>

                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-crimson">B</div>
                            <div class="role-info">
                                <div class="role-name">Budget Officer</div>
                                <p class="role-subtitle">Assess line-item budget allocations</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>

                        <a href="{{ route('login') }}" class="role-card-item span-full">
                            <div class="role-badge badge-crimson">F</div>
                            <div class="role-info">
                                <div class="role-name">Finance Officer</div>
                                <p class="role-subtitle">Approve purchase requests and procurement</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>
                    </div>
                </section>

                <!-- 4. Executive approval -->
                <section class="category-section">
                    <div class="category-header">
                        <h2 class="category-title">Executive approval</h2>
                        <p class="category-desc">Grant final clearance to proceed.</p>
                    </div>
                    <div class="roles-grid">
                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-gold">V</div>
                            <div class="role-info">
                                <div class="role-name">VPREI</div>
                                <p class="role-subtitle">Grant executive approval and NTP clearance</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>

                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-gold">P</div>
                            <div class="role-info">
                                <div class="role-name">SUC President</div>
                                <p class="role-subtitle">Authorize presentation of research outputs</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>
                    </div>
                </section>

                <!-- 5. System -->
                <section class="category-section">
                    <div class="category-header">
                        <h2 class="category-title">System</h2>
                        <p class="category-desc">Keep the portal running.</p>
                    </div>
                    <div class="roles-grid single-item-grid">
                        <a href="{{ route('login') }}" class="role-card-item">
                            <div class="role-badge badge-white">A</div>
                            <div class="role-info">
                                <div class="role-name">Administrator</div>
                                <p class="role-subtitle">Manage system roles and announcements</p>
                            </div>
                            <i class="bi bi-chevron-right chevron-icon"></i>
                        </a>
                    </div>
                </section>
            </div>

            <!-- Registration Call-to-Action -->
            <section class="register-section">
                <p class="register-text">Don't have an account yet?</p>
                <a href="{{ route('register') }}" class="btn-register-ua">
                    <i class="bi bi-person-plus-fill"></i> Register as a new user
                </a>
            </section>
        </main>

        <!-- Footer -->
        <footer class="portal-footer">
            &copy; {{ date('Y') }} University of Antique • URLC Research Portal. All rights reserved.
        </footer>
    </body>
</html>
