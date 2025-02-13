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

    <title>{{ config('app.name') }} - Your reliable and Secure hosting service provider </title>
    <!-- Preconnect to Google Fonts and Google Fonts Static -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Importing Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,500;0,600;0,700;1,400;1,800&display=swap"
        rel="stylesheet">
    <!-- all styles -->
    <link rel="preload stylesheet" href="assets/css/plugins.min.css" as="style">
    <!-- fontawesome css -->
    <link rel="preload stylesheet" href="assets/css/plugins/fontawesome.min.css" as="style">
    <!-- Custom css -->
    <link rel="preload stylesheet" href="assets/css/style.css" as="style">
</head>

<body class="loaded">

    <!-- HEADER AREA -->
    <header class="rts-header style-one header__default">
        <!-- HEADER TOP AREA -->
        <div class="rts-ht rts-ht__bg">
            <div class="container">
                <div class="row">
                    <div class="rts-ht__wrapper">
                        <div class="rts-ht__email">
                            <a href="mailto:{{ $settings->email }}"><img src="assets/images/icon/email.svg"
                                    alt="" class="icon">{{ $settings->email }}</a>
                        </div>

                        <div class="rts-ht__links">
                            <div class="live-chat-has-dropdown">
                                <a href="#" class="live__chat"><img src="assets/images/icon/forum.svg"
                                        alt="" class="icon">Live Chat</a>
                            </div>
                            <div class="login-btn-has-dropdown">
                                <a href="#" class="login__link"><img src="assets/images/icon/person.svg"
                                        alt="" class="icon">Login</a>
                                <div class="login-submenu">
                                    <form action="#">
                                        <div class="form-inner">
                                            <div class="single-wrapper">
                                                <input type="email" placeholder="Your email" required>
                                            </div>
                                            <div class="single-wrapper">
                                                <input type="password" placeholder="Password" required>
                                            </div>
                                            <div class="form-btn">
                                                <button type="submit" class="primary__btn">Log In</button>
                                            </div>
                                            <a href="#" class="forgot-password">Forgot your password?</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- HEADER TOP AREA END -->
        <div class="container">
            <div class="row">
                <div class="rts-header__wrapper">
                    <!-- FOR LOGO -->
                    <div class="rts-header__logo">
                        <a href="index.html" class="site-logo">
                            <img class="logo-white" src="assets/images/logo/logo-1.svg" alt="Hostie">
                            <img class="logo-dark" src="assets/images/logo/logo-4.svg" alt="Hostie">
                        </a>
                    </div>
                    <!-- FOR NAVIGATION MENU -->

                    <nav class="rts-header__menu" id="mobile-menu">
                        <div class="hostie-menu">
                            <ul class="list-unstyled hostie-desktop-menu">
                                <li class="menu-item hostie">
                                    <a href="{{ route('home') }}" class="hostie-dropdown-main-element">Home</a>
                                </li>

                                <li class="menu-item hostie-has-dropdown mega-menu">
                                    <a href="#" class="hostie-dropdown-main-element">Hosting</a>
                                    <div class="rts-mega-menu">
                                        <div class="wrapper">
                                            <div class="row g-0">
                                                <div class="col-lg-12">
                                                    <ul class="mega-menu-item">
                                                        <li>
                                                            <a href="#">
                                                                <img src="{{ asset('assets/images/mega-menu/22.svg') }}"
                                                                    alt="icon">
                                                                <div class="info">
                                                                    <p>Shared Hosting</p>
                                                                    <span>Manage Shared Hosting</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#">
                                                                <img src="{{ asset('assets/images/mega-menu/27.svg') }}"
                                                                    alt="icon">
                                                                <div class="info">
                                                                    <p>VPS - High Storage</p>
                                                                    <span>Get your highest storage VPS</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#">
                                                                <img src="{{ asset('assets/images/mega-menu/24.svg') }}"
                                                                    alt="icon">
                                                                <div class="info">
                                                                    <p>VPS High Performance</p>
                                                                    <span>Dedicated resources</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="menu-item hostie-has-dropdown">
                                    <a href="#" class="hostie-dropdown-main-element">Domain</a>
                                    <ul class="hostie-submenu list-unstyled menu-pages">
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('domains.index') }}">Domain
                                                Checker</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Domain
                                                Transfer</a></li>
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('domains.index') }}">Domain
                                                Registration</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Whois</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item hostie-has-dropdown">
                                    <a href="#" class="hostie-dropdown-main-element">Services</a>
                                    <ul class="hostie-submenu list-unstyled menu-pages">
                                        <li class="nav-item"><a class="nav-link" href="#">Web Application</a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link" href="#">Mobile
                                                Development</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Mobile data
                                                Collection</a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link" href="#">IT Consultancy</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item hostie-has-dropdown">
                                    <a href="#" class="hostie-dropdown-main-element">Help Center</a>
                                    <ul class="hostie-submenu list-unstyled menu-pages">
                                        <li class="nav-item"><a class="nav-link" href="#">FAQ</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Support</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                                        <li class="nav-item"><a class="nav-link"
                                                href="knowledgebase.html">Knowledgebase</a></li>

                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <!-- FOR HEADER RIGHT -->
                    <div class="rts-header__right">
                        <a href="#" class="login__btn" target="_blank">Client Area</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- HEADER AREA END -->

    <!-- HERO BANNER ONE -->
    <section class="rts-hero rts-hero__one banner-style-home-one">
        <div class="container">
            <div class="rts-hero__blur-area"></div>
            <div class="row align-items-end position-relative">
                <div class="col-lg-6">
                    <div class="rts-hero__content w-550">
                        <h6 data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M23.9799 11.9805C23.9799 10.3545 23.2659 8.8205 22.0549 7.8565C22.1949 6.2345 21.6149 4.6455 20.4649 3.4945C19.3149 2.3455 17.7299 1.7635 16.1879 1.9395C14.1739 -0.616499 9.82288 -0.664499 7.85588 1.9045C4.62288 1.5205 1.51388 4.5645 1.93988 7.7725C-0.616121 9.7865 -0.665121 14.1375 1.90488 16.1055C1.76488 17.7275 2.34488 19.3165 3.49488 20.4675C4.64488 21.6165 6.23188 22.1985 7.77188 22.0225C9.78588 24.5785 14.1369 24.6265 16.1039 22.0575C17.7239 22.1965 19.3139 21.6185 20.4649 20.4675C21.6139 19.3175 22.1939 17.7275 22.0199 16.1905C23.2659 15.1425 23.9799 13.6085 23.9799 11.9825V11.9805ZM7.97988 8.9805C7.98588 7.6725 9.97388 7.6725 9.97988 8.9805C9.97388 10.2885 7.98588 10.2885 7.97988 8.9805ZM10.8119 15.5355C10.5039 15.9985 9.87888 16.1165 9.42488 15.8125C8.96488 15.5065 8.84088 14.8855 9.14788 14.4255L13.1479 8.4255C13.4539 7.9665 14.0739 7.8405 14.5349 8.1485C14.9949 8.4545 15.1189 9.0755 14.8119 9.5355L10.8119 15.5355ZM14.9799 15.9805C13.6719 15.9745 13.6719 13.9865 14.9799 13.9805C16.2879 13.9865 16.2879 15.9745 14.9799 15.9805Z"
                                    fill="#FFC107" />
                            </svg>
                            30% Discount first month purchase
                        </h6>
                        <h1 class="heading" data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">
                            Premium
                            Hosting
                            Technologies
                        </h1>
                        <p class="description" data-sal="slide-down" data-sal-delay="400" data-sal-duration="800">
                            Developing smart solutions in-house and adopting the latest speed and security technologies
                            is our passion.</p>
                        <div class="rts-hero__content--group" data-sal="slide-down" data-sal-delay="500"
                            data-sal-duration="800">
                            <a href="{{ route('register') }}" class="primary__btn white__bg">Get Started <i
                                    class="fa-regular fa-long-arrow-right"></i></a>
                            <a href="{{ route('hosting.index') }}" class="btn__zero plan__btn">Plans & Pricing <i
                                    class="fa-regular fa-long-arrow-right"></i></a>
                        </div>
                        <p data-sal="slide-down" data-sal-delay="600" data-sal-duration="800"><img
                                src="assets/images/icon/dollar.svg" alt="">Starting from <span>$2.95</span>
                            per month
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="rts-hero__images position-relative">
                        <div class="rts-hero-main">
                            <div class="image-main ">
                                <img class="main top-bottom2" src="assets/images/banner/hosting-01.svg"
                                    alt="">
                            </div>
                            <img class="hero-shape one" src="assets/images/banner/hosting.svg" alt="">
                        </div>
                        <div class="rts-hero__images--shape">
                            <div class="one top-bottom">
                                <img src="assets/images/banner/left.svg" alt="">
                            </div>
                            <div class="two bottom-top">
                                <img src="assets/images/banner/left.svg" alt="">
                            </div>
                            <div class="three top-bottom">
                                <img src="assets/images/banner/top.svg" alt="">
                            </div>
                            <div class="four bottom-top">
                                <img src="assets/images/banner/right.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- HERO BANNER ONE END -->

    <!-- BRAND AREA -->
    <div class="rts-brand rts-brand__bg--section pt-100 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="rts-brand__wrapper">
                        <div class="rts-brand__wrapper--text">
                            <h5 data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">Hosting solutions
                                trusted by the owners of <span>2,800,000</span> domains.</h5>
                            <div class="rts-brand__wrapper--text-review" data-sal="slide-down" data-sal-delay="400"
                                data-sal-duration="800">
                                <div class="review">
                                    <div class="star">Excellent <img src="assets/images/brand/review-star.svg"
                                            alt="">
                                    </div>
                                </div>
                                <div class="review__company">
                                    954 reviews on <img src="assets/images/brand/trust-pilot.svg" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="rts-brand__slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/01.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/02.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/03.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/04.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/05.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/06.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"> <img
                                                src="assets/images/brand/07.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/08.svg" alt=""></a>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="rts-brand__slider--single">
                                        <a href="#" aria-label="brand-link"><img
                                                src="assets/images/brand/01.svg" alt=""></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- BRAND AREA END-->

    <!-- HOSTING OPTION -->
    <div class="rts-hosting-type">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="rts-hosting-type__section">
                        <h3 class="title" data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">
                            Multiple
                            Hosting Options</h3>
                        <p data-sal="slide-down" data-sal-delay="400" data-sal-duration="800">No matter your hosting
                            requirements, our platform will fit your needs.</p>
                        <div class="rts-slider__btn hosting-slider">
                            <div class="slide__btn rts-prev"><i class="fa-light fa-arrow-left"></i></div>
                            <div class="slide__btn rts-next"><i class="fa-light fa-arrow-right"></i></div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- hosting option -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="rts-hosting-type__slider">
                        <div class="swiper-wrapper">
                            <!-- single package -->
                            <div class="swiper-slide">
                                <div class="rts-hosting-type__single">
                                    <div class="hosting-icon">
                                        <img src="assets/images/hosting/03.svg" alt="">
                                    </div>
                                    <a href="wordpress-hosting.html" class="title">WordPress Hosting</a>
                                    <p class="excerpt">Manage your WordPress sites easily and more freedom.</p>
                                    <h6 class="price__start">Starting from $2.95/month</h6>
                                    <a href="wordpress-hosting.html" class="primary__btn border__btn">See Plan <i
                                            class="fa-regular fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                            <!-- single package end -->
                            <!-- single package -->
                            <div class="swiper-slide">
                                <div class="rts-hosting-type__single">
                                    <div class="hosting-icon">
                                        <img src="assets/images/hosting/04.svg" alt="">
                                    </div>
                                    <a href="wordpress-hosting.html" class="title">Web Hosting</a>
                                    <p class="excerpt">Manage your WordPress sites easily and more freedom.</p>
                                    <h6 class="price__start">Starting from $2.95/month</h6>
                                    <a href="wordpress-hosting.html" class="primary__btn border__btn">See Plan <i
                                            class="fa-regular fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                            <!-- single package end -->
                            <!-- single package -->
                            <div class="swiper-slide">
                                <div class="rts-hosting-type__single">
                                    <div class="hosting-icon">
                                        <img src="assets/images/hosting/02.svg" alt="">
                                    </div>
                                    <a href="vps-hosting.html" class="title">Vps Hosting</a>
                                    <p class="excerpt">Manage your WordPress sites easily and more freedom.</p>
                                    <h6 class="price__start">Starting from $2.95/month</h6>
                                    <a href="vps-hosting.html" class="primary__btn border__btn">See Plan <i
                                            class="fa-regular fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                            <!-- single package end -->
                            <!-- single package -->
                            <div class="swiper-slide">
                                <div class="rts-hosting-type__single">
                                    <div class="hosting-icon">
                                        <img src="assets/images/hosting/01.svg" alt="">
                                    </div>
                                    <a href="shared-hosting.html" class="title">Shared Hosting</a>
                                    <p class="excerpt">Manage your WordPress sites easily and more freedom.</p>
                                    <h6 class="price__start">Starting from $2.95/month</h6>
                                    <a href="shared-hosting.html" class="primary__btn border__btn">See Plan <i
                                            class="fa-regular fa-long-arrow-right"></i></a>
                                </div>
                            </div>
                            <!-- single package end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- HOSTING OPTION END -->

    <!-- ABOUT US -->
    <div class="rts-about position-relative section__padding">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="rts-about__image">
                        <img src="assets/images/about/about-big.png" alt="">
                        <img src="assets/images/about/about-shape-01.svg" alt=""
                            class="shape one right-left">
                        <img src="assets/images/about/about-shape-02.svg" alt="" class="shape two">
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6">
                    <div class="rts-about__content">
                        <h3 data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">We build Our Business
                            For Your Success.
                        </h3>
                        <p class="description" data-sal="slide-down" data-sal-delay="400" data-sal-duration="800">
                            Whether you need a simple blog, want to highlight your
                            business, sell products through an eCommerce.
                        </p>
                        <div class="rts-about__content--single" data-sal="slide-down" data-sal-delay="500"
                            data-sal-duration="800">
                            <div class="image">

                                <img src="assets/images/about/01.svg" alt="">
                            </div>
                            <div class="description">
                                <h6>Web Hosting</h6>
                                <p>The most popular hosting plan available and comes at one of the most affordable price
                                    points.</p>
                            </div>
                        </div>
                        <div class="rts-about__content--single" data-sal="slide-down" data-sal-delay="600"
                            data-sal-duration="800">
                            <div class="image bg-2">
                                <img src="assets/images/about/02.svg" alt="">
                            </div>
                            <div class="description">
                                <h6>Managed WordPress Hosting</h6>
                                <p>Our Managed WordPress Hosting gives you speed and performance with a full set of
                                    features.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rts-about-shape"></div>
    </div>
    <!-- ABOUT US END -->

    <!-- SEARCH DOMAIN -->
    <div class="rts-domain-finder">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="rts-domain-finder__content domain-finder-bg">
                        <h3 data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">A name that looks good
                            on
                            a billboard.</h3>
                        <form action="https://hostie-whmcs.themewant.com/cart.php" class="domain-checker"
                            data-sal="slide-down" data-sal-delay="400" data-sal-duration="800">
                            <input type="text" id="domain-name" name="query"
                                placeholder="Register a domain name to start" required>
                            <input type="hidden" name="domain" value="register">
                            <input type="hidden" name="a" value="add">
                            <button type="submit" aria-label="register domain" name="domain_type">search
                                domain</button>
                        </form>
                        <div class="compare">
                            <div class="compare__list">
                                <ul>
                                    <li data-sal="slide-down" data-sal-delay="500" data-sal-duration="800">Compare:
                                    </li>
                                    <li data-sal="slide-down" data-sal-delay="600" data-sal-duration="800"><span
                                            class="ext">.com</span> only $6.19</li>
                                    <li data-sal="slide-down" data-sal-delay="700" data-sal-duration="800"><span
                                            class="ext">.net</span> only $6.19</li>
                                    <li data-sal="slide-down" data-sal-delay="800" data-sal-duration="800"><span
                                            class="ext">.info</span> only $6.19</li>
                                    <li data-sal="slide-down" data-sal-delay="900" data-sal-duration="800"><span
                                            class="ext">.org</span> only $6.19</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SEARCH DOMAIN END -->

    <!-- OUR SERVICES -->
    <section class="rts-service section__padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="rts-section text-center w-530">
                    <h3 class="rts-section__title" data-sal="slide-down" data-sal-delay="300"
                        data-sal-duration="800">We
                        Provide Hosting Solution</h3>
                    <p class="rts-section__description" data-sal="slide-down" data-sal-delay="400"
                        data-sal-duration="800">Select your solution and we will help you narrow down our best
                        high-speed options to fit your needs.
                    </p>
                </div>
            </div>
            <!-- service list -->
            <div class="row">
                <div class="rts-service__wrapper">
                    <div class="rts-service__column">
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon shared__hosting">
                                <img src="assets/images/service/shared__hosting.svg" alt="">
                            </div>
                            <a href="shared-hosting.html" class="rts-service__single--title">Shared Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="shared-hosting.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon email__hosting">
                                <img src="assets/images/service/email__hosting.svg" alt="">
                            </div>
                            <a href="business-mail.html" class="rts-service__single--title">Email Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="business-mail.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->
                    </div>

                    <div class="rts-service__column">
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon wordpress__hosting">
                                <img src="assets/images/service/shared__hosting.svg" alt="">
                            </div>
                            <a href="wordpress-hosting.html" class="rts-service__single--title">WordPress Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="wordpress-hosting.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon dedicated__hosting">
                                <img src="assets/images/service/dedicated__hosting.svg" alt="">
                            </div>
                            <a href="dedicated-hosting.html" class="rts-service__single--title">dedicated Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="dedicated-hosting.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->

                    </div>

                    <div class="rts-service__column">
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon vps__hosting">
                                <img src="assets/images/service/vps__hosting.svg" alt="">
                            </div>
                            <a href="vps-hosting.html" class="rts-service__single--title">VPS Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="vps-hosting.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon eccomerce__hosting">
                                <img src="assets/images/service/eccommerce__hosting.svg" alt="">
                            </div>
                            <a href="wordpress-hosting.html" class="rts-service__single--title">eccommerce Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="wordpress-hosting.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->
                    </div>

                    <div class="rts-service__column">
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon reseller__hosting">
                                <img src="assets/images/service/resseller__hosting.svg" alt="">
                            </div>
                            <a href="reseller-hosting.html" class="rts-service__single--title">Reseller Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="reseller-hosting.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->
                        <!-- single service -->
                        <div class="rts-service__single">
                            <div class="rts-service__single--icon cloud__hosting">
                                <img src="assets/images/service/cloud__hosting.svg" alt="">
                            </div>
                            <a href="cloud-hosting.html" class="rts-service__single--title">cloud Hosting</a>
                            <p class="rts-service__single--excerpt">
                                The most popular hosting plan available and comes at one of the most.
                            </p>
                            <a href="cloud-hosting.html" class="rts-service__single--btn">View Details <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <!-- single service end -->
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- OUR SERVICES END -->

    <!-- DATA CENTER AREA -->
    <div class="rts-data-center fix section__padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="rts-section w-790 text-center">
                    <h3 class="rts-section__title" data-sal="slide-down" data-sal-delay="300"
                        data-sal-duration="800">
                        Data Centers All Around the World</h3>
                    <p class="rts-section__description" data-sal="slide-down" data-sal-delay="400"
                        data-sal-duration="800">Our web hosting, WordPress hosting, and cloud hosting plans offer
                        server
                        locations in: USA, Germany Egypt , India, Chaina, Brazil, Canada, Russia, Australia and South
                        Africa.
                    </p>
                </div>
            </div>
            <!-- data center content -->
            <div class="row">
                <div class="col-12">
                    <div class="rts-data-center__location">
                        <img src="assets/images/data__center.png" alt="data__center">
                        <ul class="round-shape">
                            <li class="one">
                                <span class="tooltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    aria-label="Canada" data-bs-custom-class="color-hostie"
                                    title="Canada">Canada</span>

                                <img src="assets/images/flag-01.svg" alt="">
                            </li>
                            <li class="two">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="Germany">Germany</span>
                                <img src="assets/images/flag-02.svg" alt="">
                            </li>
                            <li class="three">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="Russia">Russia</span>
                                <img src="assets/images/flag-03.svg" alt="">
                            </li>
                            <li class="four">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="USA">USA</span>
                                <img src="assets/images/flag-04.svg" alt="">
                            </li>
                            <li class="five">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="Egypt">egypt</span>
                                <img src="assets/images/flag-05.svg" alt="">
                            </li>
                            <li class="six">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="India">india</span>
                                <img src="assets/images/flag-06.svg" alt="">
                            </li>
                            <li class="seven">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="China">china</span>
                                <img src="assets/images/flag-07.svg" alt="">
                            </li>
                            <li class="eight">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="Brazil">Brazil</span>
                                <img src="assets/images/flag-08.svg" alt="">
                            </li>
                            <li class="nine">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="South Africa">arfa</span>
                                <img src="assets/images/flag-09.svg" alt="">
                            </li>
                            <li class="ten">
                                <span class="tolltip1" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="color-hostie" title="Australia">Australia</span>
                                <img src="assets/images/flag-10.svg" alt="">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="rts-shape">
            <div class="rts-shape__one"></div>
            <div class="rts-shape__two"></div>
            <div class="rts-shape__three"></div>
            <div class="rts-shape__four"></div>
        </div>
    </div>
    <!-- DATA CENTER AREA END -->

    <!-- FLASH SELL AREA -->
    <section class="rts-flash-sell">
        <div class="container">
            <div class="rts-flash-sell__bg">
                <div class="row align-items-center">
                    <div class="col-lg-4">
                        <div class="rts-flash-sell__title">
                            <h3 data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">Hosting Flash Sale
                            </h3>
                            <p data-sal="slide-down" data-sal-delay="400" data-sal-duration="800">For a limited time,
                                launch your website
                                with incredible savings.
                            </p>
                            <a data-sal="slide-down" data-sal-delay="500" data-sal-duration="800" href="#"
                                class="primary__btn white__bg">See Details</a>
                        </div>
                    </div>
                    <div class="col-lg-8 p--0">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="single__sell">
                                    <div class="single__sell--content">
                                        <div class="offer">for a limited Time</div>
                                        <div class="discount">67% Off</div>
                                        <span>hosting</span>
                                    </div>
                                    <div class="single__sell--image">
                                        <img src="assets/images/icon/cloud.svg" alt="">
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="single__sell">
                                    <div class="single__sell--content">
                                        <div class="offer">for a limited Time</div>
                                        <div class="discount">90% Off</div>
                                        <span>hosting</span>
                                    </div>
                                    <div class="single__sell--image">
                                        <img src="assets/images/icon/domain.svg" alt="">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FLASH SELL AREA END -->


    <!-- WHY CHOOSE US -->
    <section class="rts-whychoose section__padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 order-change">
                    <div class="rts-whychoose__content">
                        <h3 class="rts-whychoose__content--title" data-sal="slide-down" data-sal-delay="300"
                            data-sal-duration="800">
                            Why Choose Hostie Hosting for Your Hosting Needs
                        </h3>

                        <!-- single content-->
                        <div class="single" data-sal="slide-right" data-sal-delay="300" data-sal-duration="800">
                            <div class="single__image">
                                <img src="assets/images/icon/speed.svg" alt="">
                            </div>
                            <div class="single__content">
                                <h6>Up To 20xFaster Turbo</h6>
                                <p>That means better SEO rankings, lower bounce
                                    rates & higher conversion rates!</p>
                            </div>
                        </div>
                        <!-- single content-->
                        <div class="single" data-sal="slide-right" data-sal-delay="400" data-sal-duration="800">
                            <div class="single__image bg1">
                                <img src="assets/images/icon/support.svg" alt="">
                            </div>
                            <div class="single__content">
                                <h6>Guru Crew Support</h6>
                                <p>Our knowledgeable and friendly support team
                                    is available 24/7/365 to help!</p>
                            </div>
                        </div>
                        <!-- single content-->
                        <div class="single" data-sal="slide-right" data-sal-delay="500" data-sal-duration="800">
                            <div class="single__image">
                                <img src="assets/images/icon/money-back.svg" alt="">
                            </div>
                            <div class="single__content">
                                <h6>Money-Back Guarantee</h6>
                                <p>Give our high-speed hosting service a try
                                    completely risk-free!</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <div class="rts-whychoose__image">
                        <img src="assets/images/whychoose.svg" alt="">
                        <img src="assets/images/paper-plane.svg" alt="" class="shape one bottom-top">
                        <img src="assets/images/wifi.svg" alt="" class="shape two right-left">
                    </div>
                </div>
            </div>
        </div>
        <div class="rts-shape">
            <div class="rts-shape__one"></div>
        </div>
    </section>
    <!-- WHY CHOOSE US END -->

    <!-- HOSTING PLAN -->
    <section class="rts-plan section__padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="rts-section text-center w-560">
                    <h3 class="rts-section__title" data-sal="slide-down" data-sal-delay="300"
                        data-sal-duration="800">
                        Choose Your Web Hosting Plan</h3>
                    <p class="rts-section__description" data-sal="slide-down" data-sal-delay="400"
                        data-sal-duration="800">Shared hosting is the easiest, most economical way to get your website
                        connected to the Internet so you can start building it.
                    </p>
                </div>
            </div>
            <!-- PLAN -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="rts-plan__table">
                        <table class="table-bordered">
                            <!-- thead -->
                            <thead>
                                <tr>
                                    <th class="package__left">
                                        <img src="assets/images/pricing/pricing-image.svg" alt="">
                                    </th>
                                    <!-- package one -->
                                    <th class="package__item">
                                        <div class="package__item__info">
                                            <span class="package__type">Basic</span>
                                            <span class="start">Starting at $3.75/mo*</span>
                                            <form action="#">
                                                <select name="select" id="select" class="price__select">
                                                    <option value="1">$3.75/mo</option>
                                                    <option value="1">$10.75/mo</option>
                                                </select>
                                                <button type="submit" aria-label="buy package"
                                                    class="primary__btn primary__bg buy__now">By
                                                    Now</button>
                                            </form>
                                        </div>
                                    </th>
                                    <!-- top-right-corner -->
                                    <th class="package__item">
                                        <!-- table-title-3 -->
                                        <div class="package__item__info">
                                            <span class="package__type">Deluxe</span>
                                            <span class="start">Starting at $3.75/mo*</span>
                                            <form action="#">
                                                <select name="select" id="select1" class="price__select">
                                                    <option value="1">$3.75/mo</option>
                                                    <option value="1">$10.75/mo</option>
                                                </select>
                                                <button type="submit" aria-label="buy package"
                                                    class="primary__btn primary__bg buy__now">By
                                                    Now</button>
                                            </form>
                                        </div>
                                    </th>
                                    <!-- top-right-corner -->
                                    <th class="package__item">
                                        <div class="package__item__info">
                                            <span class="package__type">Ultra</span>
                                            <span class="start">Starting at $3.75/mo*</span>
                                            <form action="#">
                                                <select name="select" id="select2" class="price__select">
                                                    <option value="1">$3.75/mo</option>
                                                    <option value="1">$10.75/mo</option>
                                                </select>
                                                <button type="submit" aria-label="buy package"
                                                    class="primary__btn primary__bg buy__now">By
                                                    Now</button>
                                            </form>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- tbody -->
                            <tbody>
                                <!-- hardware -->
                                <tr data-filter="hardware" class="">
                                    <td class="package__left">Websites</td>
                                    <td class="package__item">1</td>
                                    <td class="package__item">Unlimited</td>
                                    <td class="package__item">Unlimited</td>
                                </tr>
                                <tr data-filter="hardware" class="">
                                    <td class="package__left">Disk storage</td>
                                    <td class="package__item">Unlimited</td>
                                    <td class="package__item">Unlimited</td>
                                    <td class="package__item">Unlimited</td>
                                </tr>
                                <tr data-filter="hardware" class="">
                                    <td class="package__left">Bandwidth</td>
                                    <td class="package__item">Scaleable</td>
                                    <td class="package__item">Scaleable</td>
                                    <td class="package__item">Scaleable</td>
                                </tr>
                                <tr data-filter="hardware" class="">
                                    <td class="package__left">FTP users</td>
                                    <td class="package__item">6</td>
                                    <td class="package__item">27</td>
                                    <td class="package__item">Unlimited</td>
                                </tr>
                                <tr data-filter="hardware" class="">
                                    <td class="package__left">MySQL databases</td>
                                    <td class="package__item">10</td>
                                    <td class="package__item">27</td>
                                    <td class="package__item">Unlimited</td>
                                </tr>
                                <tr data-filter="hardware" class="">
                                    <td class="package__left">Free SSl certificate</td>
                                    <td class="package__item"><i class="fa-regular fa-check"></i></td>
                                    <td class="package__item"><i class="fa-regular fa-check"></i></td>
                                    <td class="package__item"><i class="fa-regular fa-check"></i></td>
                                </tr>
                                <tr data-filter="hardware" class="">
                                    <td class="package__left">Free Domain for the first year</td>
                                    <td class="package__item"><i class="fa-regular fa-check"></i></td>
                                    <td class="package__item"><i class="fa-regular fa-check"></i></td>
                                    <td class="package__item"><i class="fa-regular fa-check"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- HOSTING PLAN END -->

    <!-- TESTIMONIAL -->
    <section class="rts-testimonial section__padding">
        <div class="container">
            <div class="row ">
                <div class="col-12 d-flex justify-content-center">
                    <div class="rts-section w-460 text-center">
                        <h3 class="rts-section__title" data-sal="slide-down" data-sal-delay="300"
                            data-sal-duration="800">Our Customers Love Us</h3>
                        <p class="rts-section__description" data-sal="slide-down" data-sal-delay="400"
                            data-sal-duration="800">From 24/7 support that acts as your extended team to incredibly
                            fast
                            website performance</p>
                    </div>
                </div>
            </div>
            <!-- testimonial -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="rts-testimonial__slider testimonial__slider--first">
                        <div class="swiper-wrapper">
                            <!-- single testimonial -->
                            <div class="swiper-slide">
                                <div class="rts-testimonial__single">
                                    <div class="rating">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div class="content">
                                        <p>Excellent option for those looking for High-End WordPress Hosting. I have
                                            been using Cloud ways.</p>
                                    </div>
                                    <div class="author__meta">
                                        <div class="author__meta--image">
                                            <img src="assets/images/testimonials/author.png" alt="">
                                        </div>
                                        <div class="author__meta--details">
                                            <a href="#">Jamie Knop</a>
                                            <span>Business Owner</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- single testimonial end -->
                            <!-- single testimonial -->
                            <div class="swiper-slide">
                                <div class="rts-testimonial__single">
                                    <div class="rating">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div class="content">
                                        <p>Excellent option for those looking for High-End WordPress Hosting. I have
                                            been using Cloud ways.</p>
                                    </div>
                                    <div class="author__meta">
                                        <div class="author__meta--image">
                                            <img src="assets/images/testimonials/author-2.png" alt="">
                                        </div>
                                        <div class="author__meta--details">
                                            <a href="#">Jahed Khan</a>
                                            <span>Business Owner</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- single testimonial end -->
                            <!-- single testimonial -->
                            <div class="swiper-slide">
                                <div class="rts-testimonial__single">
                                    <div class="rating">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div class="content">
                                        <p>Excellent option for those looking for High-End WordPress Hosting. I have
                                            been using Cloud ways.</p>
                                    </div>
                                    <div class="author__meta">
                                        <div class="author__meta--image">
                                            <img src="assets/images/testimonials/author-3.png" alt="">
                                        </div>
                                        <div class="author__meta--details">
                                            <a href="#">Samira Khan</a>
                                            <span>Digital Marketer</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- single testimonial end -->
                            <!-- single testimonial -->
                            <div class="swiper-slide">
                                <div class="rts-testimonial__single">
                                    <div class="rating">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div class="content">
                                        <p>Excellent option for those looking for High-End WordPress Hosting. I have
                                            been using Cloud ways.</p>
                                    </div>
                                    <div class="author__meta">
                                        <div class="author__meta--image">
                                            <img src="assets/images/testimonials/author.png" alt="">
                                        </div>
                                        <div class="author__meta--details">
                                            <a href="#">Jamie Knop</a>
                                            <span>Business Owner</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- single testimonial end -->
                        </div>
                        <!-- pagination dot -->
                        <div class="rts-dot__button slider-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- TESTIMONIAL END -->

    <!-- FAQ -->
    <section class="rts-faq section__padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="rts-faq__first">
                        <h3 class="title" data-sal="slide-down" data-sal-delay="300" data-sal-duration="800">
                            Got
                            questions? Well,
                            we've got answers.</h3>
                        <p data-sal="slide-down" data-sal-delay="400" data-sal-duration="800">From 24/7 support
                            that
                            acts as your extended team to incredibly fast website performance</p>
                        <img data-sal="slide-down" data-sal-delay="500" data-sal-duration="800"
                            src="assets/images/faq/faq.svg" alt="faq">
                        <div class="rts-faq__first--shape">
                            <div class="img"><img src="assets/images/faq/faq__animated.svg" alt="">
                            </div>
                            <div class="shape-one">domain</div>
                            <div class="shape-two">hosting</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <div class="rts-faq__accordion">
                        <div class="accordion accordion-flush" id="rts-accordion">
                            <div class="accordion-item active" data-sal="slide-left" data-sal-delay="300"
                                data-sal-duration="800">
                                <div class="accordion-header" id="first">
                                    <h4 class="accordion-button collapse show" data-bs-toggle="collapse"
                                        data-bs-target="#item__one" aria-expanded="false" aria-controls="item__one">
                                        Why buy a domain name from hostie?
                                    </h4>
                                </div>
                                <div id="item__one" class="accordion-collapse collapse collapse show"
                                    aria-labelledby="first" data-bs-parent="#rts-accordion">
                                    <div class="accordion-body">
                                        Above all else, we strive to deliver outstanding customer experiences. When you
                                        buy a domain name from hostie, we guarantee it will be handed over.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item" data-sal="slide-left" data-sal-delay="400"
                                data-sal-duration="800">
                                <div class="accordion-header" id="two">
                                    <h4 class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#item__two" aria-expanded="false" aria-controls="item__two">
                                        How does domain registration work?
                                    </h4>
                                </div>
                                <div id="item__two" class="accordion-collapse collapse" aria-labelledby="two"
                                    data-bs-parent="#rts-accordion">
                                    <div class="accordion-body">
                                        Above all else, we strive to deliver outstanding customer experiences. When you
                                        buy a domain name from hostie, we guarantee it will be handed over.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item" data-sal="slide-left" data-sal-delay="500"
                                data-sal-duration="800">
                                <div class="accordion-header" id="three">
                                    <h4 class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#item__three" aria-expanded="false"
                                        aria-controls="item__three">
                                        Why is domain name registration required?
                                    </h4>
                                </div>
                                <div id="item__three" class="accordion-collapse collapse" aria-labelledby="three"
                                    data-bs-parent="#rts-accordion">
                                    <div class="accordion-body">
                                        Above all else, we strive to deliver outstanding customer experiences. When you
                                        buy a domain name from hostie, we guarantee it will be handed over.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item" data-sal="slide-left" data-sal-delay="600"
                                data-sal-duration="800">
                                <div class="accordion-header" id="four">
                                    <h4 class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#item__four" aria-expanded="false"
                                        aria-controls="item__four">
                                        Why is domain name registration required?
                                    </h4>
                                </div>
                                <div id="item__four" class="accordion-collapse collapse" aria-labelledby="four"
                                    data-bs-parent="#rts-accordion">
                                    <div class="accordion-body">
                                        Above all else, we strive to deliver outstanding customer experiences. When you
                                        buy a domain name from hostie, we guarantee it will be handed over.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FAQ END -->
    <!-- CTA AREA -->
    <div class="rts-cta">
        <div class="container">
            <div class="row">
                <div class="rts-cta__wrapper">
                    <div class="rts-cta__left">
                        <h3 class="cta__title" data-sal="slide-down" data-sal-delay="300"
                            data-sal-duration="800">
                            Experience the Hostie Hosting Difference Today!</h3>
                        <p data-sal="slide-down" data-sal-delay="400" data-sal-duration="800">Above all else, we
                            strive
                            deliver outstanding customer experiences When you buy a domain name from.</p>
                        <a data-sal="slide-down" data-sal-delay="500" data-sal-duration="800" href="#"
                            class="primary__btn secondary__bg">get started <i
                                class="fa-regular fa-arrow-right"></i></a>
                    </div>
                    <div class="rts-cta__right">
                        <div class="cta-image">
                            <div class="cta-image__one">
                                <img src="assets/images/cta/cta__hosting.svg" alt="">
                            </div>
                            <div class="cta-image__two">
                                <img src="assets/images/cta/cta__person.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CTA AREA END  -->
    <!-- FOOTER AREA -->
    <footer class="rts-footer site-footer-one section__padding">
        <div class="container">
            <div class="row">
                <!-- widget -->
                <div class="col-lg-3 col-md-5 col-sm-6 rts-footer__widget--column">
                    <div class="rts-footer__widget footer__widget w-280">
                        <a href="index.html" aria-label="main page link" class="footer__logo">
                            <img src="assets/images/logo/footer__one__logo.svg" alt="">
                        </a>
                        <p class="brand-desc">We’re on a mission make life easier for web developers & small
                            businesses.</p>
                        <div class="separator site-default-border"></div>
                        <div class="payment__method">
                            <h6>Payment Method</h6>
                            <ul>
                                <li><img src="assets/images/payment/visa.svg" alt=""></li>
                                <li><img src="assets/images/payment/master-card.svg" alt=""></li>
                                <li><img src="assets/images/payment/paypal.svg" alt=""></li>
                                <li><img src="assets/images/payment/american-express.svg" alt=""></li>
                                <li><img src="assets/images/payment/wise.svg" alt=""></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- widget end -->
                <!-- widget -->
                <div class="col-lg-2 col-md-3 col-sm-6 rts-footer__widget--column">
                    <div class="rts-footer__widget footer__widget extra-padding">
                        <h5 class="widget-title">Company</h5>
                        <div class="rts-footer__widget--menu ">
                            <ul>
                                <li><a href="about.html">About Us</a></li>
                                <li><a href="blog.html">News Feed</a></li>
                                <li><a href="contact.html">Contact</a></li>
                                <li><a href="affiliate.html">Affiliate Program</a></li>
                                <li><a href="technology.html">Our Technology</a></li>
                                <li><a href="knowledgebase.html">Knowledgebase</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- widget end -->
                <!-- widget -->
                <div class="col-lg-2 col-md-4 col-sm-6 rts-footer__widget--column">
                    <div class="rts-footer__widget footer__widget extra-padding">
                        <h5 class="widget-title">Feature</h5>
                        <div class="rts-footer__widget--menu ">
                            <ul>
                                <li><a href="domain-checker.html">Domain Checker</a></li>
                                <li><a href="domain-transfer.html">Domain Transfer</a></li>
                                <li><a href="domain-registration.html">Domain Registration</a></li>
                                <li><a href="data-centers.html">Data Centers</a></li>
                                <li><a href="whois.html">Whois</a></li>
                                <li><a href="support.html">Support</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- widget end -->
                <!-- widget -->
                <div class="col-lg-2 col-md-6 col-sm-6 rts-footer__widget--column">
                    <div class="rts-footer__widget footer__widget">
                        <h5 class="widget-title">Hosting</h5>
                        <div class="rts-footer__widget--menu">
                            <ul>
                                <li><a href="shared-hosting.html">Shared Hosting</a></li>
                                <li><a href="wordpress-hosting.html">Wordpress Hosting</a></li>
                                <li><a href="vps-hosting.html">VPS Hosting</a></li>
                                <li><a href="reseller-hosting.html">Reseller Hosting</a></li>
                                <li><a href="dedicated-hosting.html">Dedicated Hosting</a></li>
                                <li><a href="cloud-hosting.html">Cloud Hosting</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- widget end -->
                <!-- widget -->
                <div class="col-lg-3 col-md-6 rts-footer__widget--column">
                    <div class="rts-footer__widget footer__widget">
                        <h5 class="widget-title">Join Our Newsletter</h5>
                        <p>We'll send you news and offers.</p>
                        <form action="#" class="newsletter mx-40">
                            <input type="email" class="home-one" name="email" placeholder="Enter mail"
                                required>
                            <span class="icon"><i class="fa-regular fa-envelope-open"></i></span>
                            <button type="submit" aria-label="Submit"><i
                                    class="fa-regular fa-arrow-right"></i></button>
                        </form>
                        <div class="social__media">
                            <h5>social media</h5>
                            <div class="social__media--list">
                                <a href="https://www.facebook.com" aria-label="social-link" target="_blank"
                                    class="media"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com" aria-label="social-link" target="_blank"
                                    class="media"><i class="fa-brands fa-instagram"></i></a>
                                <a href="https://www.linkedin.com" aria-label="social-link" target="_blank"
                                    class="media"><i class="fa-brands fa-linkedin"></i></a>
                                <a href="https://www.x.com" aria-label="social-link" target="_blank"
                                    class="media"><i class="fa-brands fa-x-twitter"></i></a>
                                <a href="https://www.behance.com" aria-label="social-link" target="_blank"
                                    class="media"><i class="fa-brands fa-behance"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- widget end -->
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="rts-footer__copyright text-center">
                    <p>&copy; {{ config('app.name') }} {{ date('Y') }}. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- FOOTER AREA END -->

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
    <script defer src="assets/js/plugins.min.js"></script>
    <!-- main js -->
    <script defer src="assets/js/main.js"></script>
</body>

</html>
