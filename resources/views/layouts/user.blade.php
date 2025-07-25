<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Your reliable and Secure web hosting service provider">
    <meta name="keywords" content="Hosting, Domain, Transfer, Buy Domain">
    <link rel="canonical" href="https://bluhub.rw">
    <meta name="robots" content="index, follow">
    <!-- for open graph social media -->
    <meta property="og:title" content="{{ config('app.name') }} - Your reliable and Secure hosting service provider">
    <meta property="og:description" content="Your reliable and Secure hosting service provider">
    <meta property="og:image" content="{{ asset('assets/images/banner/slider-img-01.webp') }}">
    <meta property="og:url" content="https:://bluhub.rw">
    <!-- for twitter sharing -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name') }} - Your reliable and Secure hosting service provider">
    <meta name="twitter:description" content="Your reliable and Secure hosting service provider">
    <meta name="twitter:image" content="{{ asset('assets/images/banner/slider-img-01.webp') }}">
    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/fav.png">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Preconnect to Google Fonts and Google Fonts Static -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Importing Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,500;0,600;0,700;1,400;1,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- all styles -->
    <link rel="preload stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" as="style">
    <!-- fontawesome css -->
    <link rel="preload stylesheet" href="{{ asset('assets/css/plugins/fontawesome.min.css') }}" as="style">
    <!-- Custom css -->
    <link rel="preload stylesheet" href="{{ asset('assets/css/style.css') }}" as="style">
    @yield('styles')

</head>

<body class="loaded">
    <x-menu-component />

    @yield('content')
    <x-footer-component />
    <div id="anywhere-home" class="">
    </div>
   <x-sidebar-menu/>
    <div class="loader-wrapper">
        <div class="loader">
        </div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>
    </div>
    <div class="progress-wrap" style="z-index: 1000;">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919; height: 20px; width: auto;">
            </path>
        </svg>
    </div>
    <!-- BACK TO TOP AREA EDN -->

    <!-- All Plugin -->
    <script defer src="{{ asset('assets/js/plugins.min.js') }}"></script>
    <!-- main js -->
    <script defer src="{{ asset('assets/js/main.js') }}"></script>

    @yield('scripts')
</body>

</html>
