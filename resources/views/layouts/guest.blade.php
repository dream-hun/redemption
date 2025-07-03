<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="Your Ultimate Solution for Web Hosting & WHMCS">
    <meta name="keywords" content="Hosting, Domain, Transfer, Buy Domain, WHMCS">
    <link rel="canonical" href="https://bluhub.rw">
    <meta name="robots" content="index, follow">
    <!-- for open graph social media -->
    <meta property="og:title" content="Bluhub - Web Hosting & Domain registration">
    <meta property="og:description" content="Your Ultimate Solution for Web Hosting & WHMCS">
    <meta property="og:image" content="https://bluhub.rwimage.jpg">
    <meta property="og:url" content="https://bluhub.rw">
    <!-- for twitter sharing -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bluhub - Web Hosting & WHMCS Template">
    <meta name="twitter:description" content="Your Ultimate Solution for Web Hosting & WHMCS">
    <meta name="twitter:image" content="https://bluhub.rw/assets/images/banner/slider-img-01.webp">
    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/fav.png">

    <title>@yield('page-title') - {{config('app.name')}}</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <!--Load bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        body {
            font-family: "Inter", sans-serif !important;
        }
    </style>
    @livewireStyles
</head>

<body>
    {{ $slot }}

    @livewireScripts
</body>

</html>
