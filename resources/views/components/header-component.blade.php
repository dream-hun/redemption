 <!-- HEADER AREA -->
 <header class="rts-header top-0 header__style__eleven">
     <!-- HEADER TOP AREA END -->
     <div class="container">
         <div class="row">
             <div class="rts-header__wrapper">
                 <!-- FOR LOGO -->
                 <div class="rts-header__logo">
                     <a href="{{ route('home') }}" class="site-logo">
                         <img class="logo-white" src="{{ asset('logo.webp') }}" alt="{{ config('app.name') }}">
                         <img class="logo-dark" src="{{ asset('logo.webp') }}" alt="{{ config('app.name') }}">
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
                                     <li class="nav-item"><a class="nav-link" href="{{ route('domains.index') }}">Domain
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
                                     <li class="nav-item"><a class="nav-link" href="#">Mobile Development</a></li>
                                     <li class="nav-item"><a class="nav-link" href="#">Mobile data Collection</a>
                                     </li>
                                     <li class="nav-item"><a class="nav-link" href="#">IT Consultancy</a></li>
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
