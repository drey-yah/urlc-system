<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>URLC Research Portal</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <style>
            body {
                background-color: #EBF5FF;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 0;
            }
            .landing-card {
                background: white;
                border-radius: 24px;
                padding: 3rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                max-width: 1200px;
                width: 95%;
            }
            .role-card {
                border-radius: 16px;
                padding: 2rem;
                color: white;
                text-decoration: none;
                transition: all 0.3s ease;
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .role-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                color: white;
            }
            .role-icon {
                font-size: 3rem;
                margin-bottom: 1.5rem;
            }
            .btn-researcher { background-color: #2563EB; }
            .btn-reviewer { background-color: #10B981; }
            .btn-admin { background-color: #8B5CF6; }
        </style>
    </head>
    <body>
        <div class="text-center">
            <div class="mb-5">
                <i class="bi bi-file-earmark-text-fill text-primary" style="font-size: 4rem;"></i>
                <h1 class="display-4 fw-bold mt-3">URLC Research Portal</h1>
                <p class="h5 text-muted fw-normal">Research Proposal Management System</p>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <span class="badge bg-white text-muted border">Cloud Enabled</span>
                    <span class="badge bg-white text-muted border">UI Optimized</span>
                    <span class="badge bg-white text-muted border">State of the Art</span>
                </div>
            </div>

            <div class="landing-card mx-auto">
                <h2 class="h4 fw-bold mb-5">Select Your Role</h2>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card btn-researcher">
                            <i class="bi bi-person role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">Researcher</h3>
                            <p class="small mb-0 opacity-75">Submit and track your research proposals</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card btn-reviewer">
                            <i class="bi bi-person-check role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">Reviewer</h3>
                            <p class="small mb-0 opacity-75">Review and evaluate research proposals</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card" style="background-color: #F59E0B;">
                            <i class="bi bi-person-badge role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">Coordinator</h3>
                            <p class="small mb-0 opacity-75">Endorse proposals from your department</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card" style="background-color: #6366F1;">
                            <i class="bi bi-building role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">College Dean</h3>
                            <p class="small mb-0 opacity-75">Review and note college research proposals</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card" style="background-color: #0ea5e9;">
                            <i class="bi bi-inbox role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">Support Staff</h3>
                            <p class="small mb-0 opacity-75">Receive and verify proposal compliance</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card" style="background-color: #d97706;">
                            <i class="bi bi-cash-coin role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">Budget Officer</h3>
                            <p class="small mb-0 opacity-75">Evaluate line-item budget allocations</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card" style="background-color: #059669;">
                            <i class="bi bi-bank role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">Finance Officer</h3>
                            <p class="small mb-0 opacity-75">Approve purchase requests & procurement</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card" style="background-color: #1e293b;">
                            <i class="bi bi-award role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">VPREI</h3>
                            <p class="small mb-0 opacity-75">Grant executive approval & NTP clearance</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('login') }}" class="role-card btn-admin">
                            <i class="bi bi-shield-check role-icon"></i>
                            <h3 class="h5 fw-bold mb-2">Administrator</h3>
                            <p class="small mb-0 opacity-75">Manage system roles & announcements</p>
                        </a>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-center">
                    <p class="text-muted mb-3">Don't have an account yet?</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i> Register as a New User
                    </a>
                </div>
            </div>
            
            <div class="mt-5 text-muted small">
                &copy; {{ date('Y') }} URLC Research Portal. All rights reserved.
            </div>
        </div>
    </body>
</html>
