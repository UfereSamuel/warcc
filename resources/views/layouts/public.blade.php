<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Africa CDC - Western RCC Staff Management')</title>

    <!-- Favicon and App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicons/favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">

    <!-- Standard Favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">

    <!-- Web App Manifest -->
    <link rel="manifest" href="{{ asset('favicons/manifest.json') }}">

    <!-- Microsoft Tiles -->
    <meta name="msapplication-TileColor" content="#348F41">
    <meta name="msapplication-TileImage" content="{{ asset('favicons/ms-icon-144x144.png') }}">
    <meta name="msapplication-config" content="{{ asset('favicons/browserconfig.xml') }}">

    <!-- Theme Colors -->
    <meta name="theme-color" content="#348F41">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="RCC Staff">

    <!-- SEO and Social Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Africa CDC Western Regional Collaborating Centre Staff Management System - Strengthening health security across West Africa')">
    <meta name="keywords" content="Africa CDC, West Africa, health security, disease surveillance, staff management, public health">
    <meta name="author" content="Africa CDC Western RCC">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Africa CDC - Western RCC Staff Management')">
    <meta property="og:description" content="@yield('meta_description', 'Africa CDC Western Regional Collaborating Centre Staff Management System')">
    <meta property="og:image" content="{{ asset('favicons/favicon-512x512.png') }}">
    <meta property="og:site_name" content="Africa CDC Western RCC">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'Africa CDC - Western RCC Staff Management')">
    <meta property="twitter:description" content="@yield('meta_description', 'Africa CDC Western Regional Collaborating Centre Staff Management System')">
    <meta property="twitter:image" content="{{ asset('favicons/favicon-512x512.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Icons CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom-icons.css') }}">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-green: #348F41;
            --gold: #B4A269;
            --red: #782C2D;
            --yellow: #E08F2A;
            --brown: #6B4C24;
            --orange: #C45B39;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand img {
            height: 40px;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--gold) 100%);
            color: white;
            padding: 100px 0;
        }

        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .btn-primary:hover {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .text-primary {
            color: var(--primary-green) !important;
        }

        .bg-primary {
            background-color: var(--primary-green) !important;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .footer {
            background-color: var(--brown);
            color: white;
        }

        .activity-card {
            border-left: 4px solid var(--primary-green);
        }

        .activity-card.ongoing {
            border-left-color: var(--yellow);
        }

        .activity-card.completed {
            border-left-color: var(--gold);
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logos/logo.png') }}" alt="Africa CDC Logo" class="me-2">
                <span class="fw-bold text-primary">Western RCC</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.events') }}">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.contact') }}">Contact</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="btn btn-primary" href="{{ route('auth.login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Staff Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/logos/logo.png') }}" alt="Africa CDC Logo" height="40" class="me-2">
                        <span class="fw-bold">Africa CDC Western RCC</span>
                    </div>
                    <p class="mb-0">Strengthening health security and disease surveillance across West Africa through collaborative partnerships and innovative solutions.</p>
                </div>

                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="{{ route('public.about') }}" class="text-light text-decoration-none">About</a></li>
                        <li><a href="{{ route('public.events') }}" class="text-light text-decoration-none">Events</a></li>
                        <li><a href="{{ route('public.contact') }}" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 mb-4">
                    <h6 class="fw-bold mb-3">Staff Portal</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('auth.login') }}" class="text-light text-decoration-none">Staff Login</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 mb-4">
                    <h6 class="fw-bold mb-3">Contact Info</h6>
                    <p class="mb-1"><i class="fas fa-envelope me-2"></i> info@africacdc.org</p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i> +233 XXX XXX XXX</p>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Accra, Ghana</p>
                </div>
            </div>

            <hr class="my-4">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} Africa CDC Western RCC. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Staff Management System v1.0</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
