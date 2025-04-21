<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHostRequest;
use App\Models\Hosting;
use Illuminate\Http\Request;

final class HostingController extends Controller
{
    public function index()
    {
        $hostings = Hosting::all();

        return view('admin.hostings.index', ['hostings' => $hostings]);
    }

    public function create()
    {
        return view('admin.hostings.create');
    }

    public function store(StoreHostRequest $request)
    {
        $hosting = Hosting::create($request->all());

        return redirect()->route('hostings.index')->with('success', $hosting->name.' hosting has been created successfully.');

    }

    public function edit(Hosting $hosting)
    {
        return view('admin.hostings.edit', ['hosting' => $hosting]);
    }

    public function update(Request $request, Hosting $hosting): void
    {
        $hosting->update($request->all());
    }
}
