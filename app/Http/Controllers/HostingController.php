<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hosting;

final class HostingController extends Controller
{
    public function index()
    {
        $plans=Hosting::all();
        return view('hosting.shared',['plans'=>$plans]);
    }
}
