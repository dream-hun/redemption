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

    <title>Login - Web Hosting & WHMCS Template</title>
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

<body>
    {{ $slot }}
    
</body>

</html>
