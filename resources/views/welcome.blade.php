<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>URLC Research Proposal Management System</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background-color: #f8f9fa;
            }
            .hero-section {
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            }
            .navbar-brand img {
                height: 40px;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">
                    URLC System
                </a>
                <div class="ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/redirect') }}" class="btn btn-outline-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-dark me-2">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-dark">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <header class="hero-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h1 class="display-4 fw-bold mb-4">Research Proposal Management System</h1>
                        <p class="lead mb-5 text-secondary">
                            A streamlined platform for researchers to submit proposals, and for reviewers and administrators to manage the approval workflow effectively.
                        </p>
                        @auth
                            <a href="{{ url('/redirect') }}" class="btn btn-primary btn-lg px-5">Go to Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 me-3">Get Started</a>
                            <a href="#about" class="btn btn-outline-secondary btn-lg px-5">Learn More</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <section id="about" class="py-5 bg-white">
            <div class="container py-5">
                <div class="row g-5 text-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <h3 class="h4 mb-3">Submission</h3>
                            <p class="text-muted">Easily upload your research proposals in PDF format and track their progress in real-time.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <h3 class="h4 mb-3">Reviewing</h3>
                            <p class="text-muted">Reviewers provide feedback and suggestions to help improve research quality.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <h3 class="h4 mb-3">Approval</h3>
                            <p class="text-muted">Transparent approval process with final decisions made by system administrators.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="py-4 bg-light border-top">
            <div class="container text-center">
                <p class="mb-0 text-muted">&copy; {{ date('Y') }} URLC Research Proposal Management System. All rights reserved.</p>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
