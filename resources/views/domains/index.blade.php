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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        var domainCheckRoute = "{{ route('domain.check') }}";
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</head>

<body class="page-template template-resell">
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
    <section class="rts-hero-three rts-hero__one rts-hosting-banner domain-checker-padding banner-default-height">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="rts-hero__content domain">
                        <h1 data-sal="slide-down" data-sal-delay="100" data-sal-duration="800" class="sal-animate">
                            Find
                            Best Unique Domains
                            Checker!
                        </h1>
                        <p class="description sal-animate" data-sal="slide-down" data-sal-delay="200"
                            data-sal-duration="800">Web
                            Hosting, Domain Name and Hosting Center Solutions</p>
                        <form id="domainForm" action="{{ route('domain.check') }}" method="POST"
                            data-sal-delay="300" data-sal-duration="800">
                            @csrf
                            <div class="rts-hero__form-area">

                                <input type="text" placeholder="Type your domain without extension Ex: jhonsmith"
                                    name="domains" id="domainText" autocomplete="off">
                                <div class="select-button-area">

                                    <button id="checkButton" type="submit">Search</button>
                                </div>
                            </div>
                        </form>
                        <div class="banner-content-tag" data-sal-delay="400" data-sal-duration="800">
                            <p class="desc">Popular Domain:</p>
                            <ul class="tag-list">
                                @foreach ($tlds as $tld)
                                    <li><span>{{ $tld->tld }}</span><span>{{ $tld->formatedRegisterPrice() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="banner-shape-area">
            <img class="three" src="assets/images/banner/banner-bg-element.svg" alt="">
        </div>
    </section>
    <section class="rts-domain-pricing-area pt--120 pb--120">
        <div class="container">

            <div class="row justify-content-center">
                <div class="section-title-area w-570">
                    <h2 class="section-title sal-animate" data-sal="slide-down" data-sal-delay="100"
                        data-sal-duration="800">Bluhub
                        Straight forward Domain Pricing</h2>
                    <p class="desc sal-animate" data-sal="slide-down" data-sal-delay="200" data-sal-duration="800">
                        Straightforward
                        Domain Pricing</p>
                </div>
            </div>
            <div class="section-inner" id="results">
                <div class="row g-5">
                    <div id="errorMessage" class="alert alert-danger hidden"></div>
                    <div id="resultsContainer" class="col-lg-12">
                        <!-- Results will be appended here -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-faq-component />

    <script>
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initially hide the error message
            $('#errorMessage').hide();

            // Handle domain check form submission
            $('#domainForm').on('submit', function(e) {
                e.preventDefault();

                const $button = $('#checkButton');
                const $results = $('#results');
                const $resultsContainer = $('#resultsContainer');
                const $errorMessage = $('#errorMessage');

                // Clear previous results
                $results.hide();
                $errorMessage.hide();
                $resultsContainer.empty();

                // Get and validate domain input
                const domain = $('#domainText').val().trim();
                if (!domain) {
                    $errorMessage.text('Please enter a domain name.').show();
                    return;
                }

                // Disable button and show loading state
                $button.prop('disabled', true).text('Checking...');

                // Perform domain check
                $.ajax({
                    url: "{{ route('domain.check') }}",
                    method: 'POST',
                    data: {
                        domains: domain
                    },
                    success: function(response) {
                        $results.show();

                        if (response.error) {
                            $errorMessage.text(response.error).show();
                        } else if (response.results) {
                            $resultsContainer.empty(); // Clear previous results

                            Object.entries(response.results).forEach(([domain, result]) => {
                                const availabilityClass = result.available ?
                                    'available' :
                                    'unavailable';
                                const resultHtml = `
                                <div class="col-lg-4 col-xl-3 col-md-3 col-sm-6 sal-animate" data-sal="slide-down" data-sal-delay="200" data-sal-duration="800">
                                    <div class="pricing-wrapper ${availabilityClass}">
                                        <div class="logo"><img src="assets/images/pricing/domain-01.svg" alt=""></div>
                                        <div class="content">
                                            <p class="desc">${domain} is ${result.available ? 'available!' : 'unavailable.'}</p>
                                            <div class="price-area">
                                                ${result.available ? `<span class="now">RWF ${result.register_price}</span>` : ''}
                                            </div>
                                            <div class="button-area">
                                                ${result.available ? `
                                                                                                                                <button type="button" class="pricing-btn rts-btn addToCartButton"
                                                                                                                                    data-domain="${domain}" data-price="${result.register_price}">
                                                                                                                                    Add to Cart
                                                                                                                                </button>
                                                                                                                            ` : `<p>${result.reason || 'Not available'}</p>`}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                                $resultsContainer.append(resultHtml);
                            });
                            $resultsContainer.addClass('row g-5');
                        }
                    },
                    error: function(xhr) {
                        $errorMessage.text(xhr.responseJSON?.error ||
                            'An error occurred while checking domains.').show();
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('Check Availability');
                    }
                });
            });


            // Handle Add to Cart using event delegation
            $(document).on('click', '.addToCartButton', function() {
                const button = $(this);
                const domain = button.data('domain');
                const price = button.data('price');

                button.prop('disabled', true).text('Adding...');

                $.ajax({
                    url: '/add-to-cart', // STILL INCORRECT - MUST BE NAMED ROUTE
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        domain: domain,
                        price: price
                    },
                    success: function(response) {
                        alert(response.message);
                        window.location.href = "{{ route('register') }}";
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                        alert(xhr.responseJSON ? JSON.stringify(xhr.responseJSON) :
                            'Failed to add to cart.');
                        button.prop('disabled', false).text('Add to Cart');
                    }
                });
            });
        });
    </script>
</body>

</html>
