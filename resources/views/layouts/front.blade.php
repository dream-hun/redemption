<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- all styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bluhub-blue: #2196F3;
            --bluhub-dark-blue: #1976D2;
        }

        /* Top Navigation */
        .top-nav {
            background-color: var(--bluhub-blue);
            padding: 8px 0;
            color: white;
            font-size: 14px;
        }

        .support-email {
            display: flex;
            align-items: center;
        }

        .support-email i {
            margin-right: 5px;
        }

        .top-nav-right {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .live-chat, .cart {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .live-chat:hover, .cart:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .live-chat i, .cart i {
            margin-right: 5px;
        }

        .rwf-badge {
            background-color: transparent;
            border: 1px solid white;
            border-radius: 4px;
            padding: 1px 6px;
            margin-left: 5px;
            font-size: 12px;
        }

        /* Main Navigation */
        .main-nav {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px 0;
        }

        .navbar-brand img {
            height: 50px;
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            margin: 0 10px;
        }

        .nav-link:hover {
            color: var(--bluhub-blue) !important;
        }

        .dropdown-toggle::after {
            vertical-align: middle;
        }

        .login-btn {
            background-color: var(--bluhub-blue);
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            padding: 8px 30px;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: var(--bluhub-dark-blue);
        }

        /* Mobile adjustments */
        @media (max-width: 992px) {
            .top-nav {
                text-align: center;
            }

            .top-nav-right {
                justify-content: center;
                margin-top: 5px;
            }
        }
    </style>


</head>

<body>

    <div class="top-nav">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 support-email">
                    <i class="bi bi-envelope-fill"></i>
                    <span>support@bluhub.rw</span>
                </div>
                <div class="col-lg-6 top-nav-right">
                    <a href="#" class="live-chat">
                        <i class="bi bi-chat-fill"></i>
                        <span>Live Chat</span>
                    </a>
                    <a href="#" class="cart">
                       <livewire:cart-total/>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <iframe src="{{ route('cart.menu') }}" style="width: 100%; height: 600px; border: none;"></iframe>

    <div class="container-fluid">
        @yield('content')
    </div>


    @include('custom.cart-footer')
    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
