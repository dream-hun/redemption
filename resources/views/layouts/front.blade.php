<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Importing Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,500;0,600;0,700;1,400;1,800&display=swap"
        rel="stylesheet">
    <!-- all styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .top-navbar {
            background-color: #4291FC;
            color: white;
        }

        .logo-navbar {
            background-color: white;
            border-bottom: 1px solid #e5e5e5;
            padding: 10px 0;
        }

        .namecheap-logo {
            height: 40px;
        }

        .search-icon {
            cursor: pointer;
        }

        .nav-btn {
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .cart-btn {
            color: white;
            text-decoration: none;
        }

        .footer {
            background-color: #2d3436;
            color: #ffffff;
            padding: 20px 0;
            font-size: 14px;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer-logo {
            height: 40px;
            margin-right: 15px;
        }

        .footer-links {
            display: flex;
            gap: 15px;
        }

        .address {
            color: #999;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <!-- Top dark navbar -->
    <nav class="navbar navbar-expand-lg top-navbar py-0">
        <div class="container">
            <!-- Left side nav items -->
            <div class="navbar-nav me-auto">
                <div class="nav-item">
                    <a class="nav-link nav-btn" href="#">
                        CONTACT US
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link nav-btn" href="{{ route('login') }}">SIGN IN</a>
                </div>
                <div class="nav-item">
                    <a class="nav-link nav-btn" href="{{ route('register') }}">SIGN UP</a>
                </div>
            </div>

            <!-- Right side nav items -->
            <div class="navbar-nav ms-auto">

                <div class="nav-item">
                    <a class="nav-link cart-btn" href="#">
                        <span class="bi bi-cart-fill me-1"></span>
                        {{ Cknow\Money\Money::RWF($total) }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Logo navbar -->
    <nav class="navbar navbar-expand-lg logo-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('logo.webp') }}"alt="Namecheap" class="namecheap-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    @yield('content')

    <footer class="footer mt-auto position-fixed bottom-0 w-100">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left side with logo and copyright text -->
                <div class="col-md-7 d-flex flex-md-row flex-column">
                    <img src="{{ asset('logo.webp') }}" alt="{{ config('app.name') }}" class="footer-logo">
                    <div>
                        <div>The entirety of this site is protected by copyright © 2019-{{ date('Y') }}
                            {{ config('app.name') }}.</div>
                        <div class="address">{{ $settings->address }}</div>
                    </div>
                </div>

                <!-- Right side with links -->
                <div class="col-md-5 mt-3 mt-md-0">
                    <div class="footer-links d-flex flex-wrap justify-content-md-end">
                        <a href="#">Terms and Conditions</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">UDRP</a>
                        <a href="#">Cookie Preferences</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
