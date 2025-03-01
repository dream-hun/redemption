<?php

return [
    'host' => env('EPP_HOST'),
    'username' => env('EPP_USERNAME'),
    'password' => env('EPP_PASSWORD'),
    'port' => env('EPP_PORT'),
    'ssl' => env('EPP_SSL'),
    'certificate' => storage_path('app/certificate/certificate.pem'),

];
