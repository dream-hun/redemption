<?php

namespace App\Http\Controllers;

use App\Models\Hosting;
use Illuminate\Http\Request;

class SharedHostingController extends Controller
{
    public function index()
    {
        $plans=Hosting::where('category_id',1)->whereColumn('status','active')->get();
        return view('hosting.shared',['plans'=>$plans]);
    }
}
