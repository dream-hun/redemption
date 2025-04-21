<?php

declare(strict_types=1);

namespace App\Http\Controllers;

final class HostingController extends Controller
{
    public function index()
    {
        return view('hosting.index');
    }
}
