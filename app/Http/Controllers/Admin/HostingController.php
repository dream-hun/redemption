<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHostRequest;
use App\Models\Hosting;
use Illuminate\Http\Request;

class HostingController extends Controller
{
    public function index()
    {
        $hostings = Hosting::all();
        return view('admin.hostings.index', compact('hostings'));
    }

    public function create()
    {
        return view('admin.hostings.create');
    }
    public function store(StoreHostRequest $request)
    {
        $hosting=Hosting::create($request->all());
        return redirect()->route('hostings.index')->with('success', $hosting->name.' hosting has been created successfully.');

    }

    public function edit(Hosting $hosting)
    {
        return view('admin.hostings.edit', compact('hosting'));
    }
    public function update(Request $request, Hosting $hosting)
    {
        $hosting->update($request->all());
    }
}
