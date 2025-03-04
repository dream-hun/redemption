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
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
    <link rel="preload stylesheet" href="assets/css/style.css" as="style">
    @yield('styles')

</head>
<body class="bg-light">
    <x-menu-component />
    @yield('content')
    </body>


    </html>
