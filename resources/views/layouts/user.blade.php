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
    <link rel="preload stylesheet" href="assets/css/plugins.min.css" as="style">
    <!-- fontawesome css -->
    <link rel="preload stylesheet" href="assets/css/plugins/fontawesome.min.css" as="style">
    <!-- Custom css -->
    <link rel="preload stylesheet" href="assets/css/style.css" as="style">
    @yield('styles')

</head>

<body class="loaded">
    <x-menu-component />

    @yield('content')
    <x-footer-component />
    <div id="anywhere-home" class="">
    </div>

    <!-- side bar area  -->
    <div id="side-bar" class="side-bar header-two">
        <button class="close-icon-menu" aria-label="Close Menu"><i class="fa-sharp fa-thin fa-xmark"></i></button>
        <!-- mobile menu area start -->
        <div class="mobile-menu-main">
            <nav class="nav-main mainmenu-nav mt--30">
                <ul class="mainmenu metismenu" id="mobile-menu-active">
                    <li class="has-droupdown">
                        <a href="#" class="main">Home</a>
                        <ul class="submenu mm-collapse">
                            <li><a class="mobile-menu-link" href="index.html">Home One</a></li>
                            <li><a class="mobile-menu-link" href="index-two.html">Home Two
                                </a></li>
                            <li><a class="mobile-menu-link" href="index-three.html">Home Three</a></li>
                            <li><a class="mobile-menu-link" href="index-four.html">Home Four</a></li>
                            <li><a class="mobile-menu-link" href="index-five.html">Home Five</a></li>
                            <li><a class="mobile-menu-link" href="index-six.html">Home Six</a></li>
                            <li><a class="mobile-menu-link" href="index-sevent.html">Game Hosting</a></li>
                            <li><a class="mobile-menu-link" href="index-eight.html">Cloud Hosting</a></li>
                            <li><a class="mobile-menu-link" href="index-nine.html">WP Hosting</a></li>
                            <li><a class="mobile-menu-link" href="index-ten.html">Mern Hosting</a></li>
                            <li><a class="mobile-menu-link" href="index-eleven.html">Premium Hosting</a></li>
                        </ul>
                    </li>
                    <li class="has-droupdown">
                        <a href="#" class="main">Pages</a>
                        <ul class="submenu mm-collapse">
                            <li><a class="mobile-menu-link" href="about.html">About</a></li>
                            <li><a class="mobile-menu-link" href="team.html">Affiliate</a></li>
                            <li><a class="mobile-menu-link" href="faq.html">Pricing</a></li>
                            <li><a class="mobile-menu-link" href="book-a-demo.html">Sign Up</a></li>
                            <li><a class="mobile-menu-link" href="free-audit.html">Whois</a></li>
                            <li><a class="mobile-menu-link" href="pricing.html">Partner</a></li>
                            <li><a class="mobile-menu-link" href="blog.html">Blog</a></li>
                            <li><a class="mobile-menu-link" href="blog-list.html">Blog List</a></li>
                            <li><a class="mobile-menu-link" href="blog-grid-2.html">Blog Grid</a></li>
                            <li><a class="mobile-menu-link" href="support.html">Support</a></li>
                            <li><a class="mobile-menu-link" href="pricing.html">Pricing</a></li>
                            <li><a class="mobile-menu-link" href="pricing-two.html">Pricing Package</a></li>
                            <li><a class="mobile-menu-link" href="pricing-three.html">Pricing Comparison</a></li>
                            <li><a class="mobile-menu-link" href="signin.html">Sign In</a></li>
                            <li><a class="mobile-menu-link" href="business-mail.html">Business Mail</a></li>
                            <li><a class="mobile-menu-link" href="knowledgebase.html">Knowledgebase</a></li>
                            <li><a class="mobile-menu-link" href="blog-details.html">Blog Details</a></li>
                            <li><a class="mobile-menu-link" href="domain-checker.html">Domain Checker</a></li>
                            <li><a class="mobile-menu-link" href="ssl-certificate.html">SSL Certificates</a></li>
                            <li><a class="mobile-menu-link" href="data-center.html">Data Centers</a></li>
                            <li><a class="mobile-menu-link" href="technology.html">Technology</a></li>
                            <li><a class="mobile-menu-link" href="contact.html">Contact</a></li>
                            <li><a class="mobile-menu-link" href="domain-transfer.html">Domain Transfer</a></li>
                            <li><a class="mobile-menu-link" href="payment-method.html">Payment Method</a></li>
                        </ul>
                    </li>
                    <li class="has-droupdown">
                        <a href="#" class="main">Hosting</a>
                        <ul class="submenu mm-collapse">
                            <li><a class="mobile-menu-link" href="shared-hosting.html">Shared Hosting</a></li>
                            <li><a class="mobile-menu-link" href="wordpress-hosting.html">WordPress Hosting</a></li>
                            <li><a class="mobile-menu-link" href="vps-hosting.html">VPS Hosting</a></li>
                            <li><a class="mobile-menu-link" href="reseller-hosting.html">Reseller Hosting</a></li>
                            <li><a class="mobile-menu-link" href="dedicated-hosting.html">Dedicated Hosting</a></li>
                            <li><a class="mobile-menu-link" href="cloud-hosting.html">Cloud Hosting</a></li>
                        </ul>
                    </li>
                    <li class="has-droupdown">
                        <a href="#" class="main">Domain</a>
                        <ul class="submenu mm-collapse">
                            <li><a class="mobile-menu-link" href="domain-checker.html">Domain Checker</a></li>
                            <li><a class="mobile-menu-link" href="domain-transfer.html">Domain Transfer</a></li>
                        </ul>
                    </li>
                    <li class="has-droupdown">
                        <a href="#" class="main">Technology</a>
                        <ul class="submenu mm-collapse">
                            <li><a class="mobile-menu-link" href="technology.html">Technology</a></li>
                            <li><a class="mobile-menu-link" href="data-center.html">Data Center</a></li>
                            <li><a class="mobile-menu-link" href="game-details.html">Game Details</a></li>
                        </ul>
                    </li>
                    <li class="has-droupdown">
                        <a href="#" class="main">Help Center</a>
                        <ul class="submenu mm-collapse">
                            <li><a class="mobile-menu-link" href="knowledgebase.html">Knowledgebase</a></li>
                            <li><a class="mobile-menu-link" href="hosting-offer-one.html">Ads Banner</a></li>
                            <li><a class="mobile-menu-link" href="whois.html">Whois</a></li>
                            <li><a class="mobile-menu-link" href="support.html">Support</a></li>
                            <li><a class="mobile-menu-link" href="contact.html">Contact</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <ul class="social-area-one pl--20 mt--100">
                <li><a href="https://www.linkedin.com" aria-label="social-link" target="_blank"><i
                            class="fa-brands fa-linkedin"></i></a></li>
                <li><a href="https://www.x.com" aria-label="social-link" target="_blank"><i
                            class="fa-brands fa-twitter"></i></a></li>
                <li><a href="https://www.youtube.com" aria-label="social-link" target="_blank"><i
                            class="fa-brands fa-youtube"></i></a></li>
                <li><a href="https://www.facebook.com" aria-label="social-link" target="_blank"><i
                            class="fa-brands fa-facebook-f"></i></a></li>
            </ul>
        </div>
        <!-- mobile menu area end -->
    </div>

    <!-- side abr area end -->


    <!-- THEME PRELOADER START -->
    <div class="loader-wrapper">
        <div class="loader">
        </div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>
    </div>
    <!-- THEME PRELOADER END -->
    <!-- BACK TO TOP AREA START -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
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
