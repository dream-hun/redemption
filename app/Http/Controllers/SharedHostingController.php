<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hosting;

final class SharedHostingController extends Controller
{
    public function index()
    {
        $plans = Hosting::where('category_id', 1)->orWhere('status', 'active')->get();

        return view('hosting.shared', ['plans' => $plans]);
    }
}
