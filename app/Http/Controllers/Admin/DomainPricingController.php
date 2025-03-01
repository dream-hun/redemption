<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDomainPricingRequest;
use App\Http\Requests\Admin\UpdateDomainPricingRequest;
use App\Models\DomainPricing;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DomainPricingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('domain_pricing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domainPricings = DomainPricing::all();

        return view('admin.domainPricings.index', compact('domainPricings'));
    }

    public function create()
    {
        abort_if(Gate::denies('domain_pricing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.domainPricings.create');
    }

    public function store(StoreDomainPricingRequest $request)
    {
        $domainPricing = DomainPricing::create($request->all());

        return redirect()->route('admin.domain-pricings.index');
    }

    public function edit(DomainPricing $domainPricing)
    {
        abort_if(Gate::denies('domain_pricing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.domainPricings.edit', compact('domainPricing'));
    }

    public function update(UpdateDomainPricingRequest $request, DomainPricing $domainPricing)
    {
        $domainPricing->update($request->all());

        return redirect()->route('admin.domain-pricings.index');
    }

    public function destroy(DomainPricing $domainPricing)
    {
        abort_if(Gate::denies('domain_pricing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domainPricing->delete();

        return back();
    }

    public function massDestroy(MassDestroyDomainPricingRequest $request)
    {
        $domainPricings = DomainPricing::find(request('ids'));

        foreach ($domainPricings as $domainPricing) {
            $domainPricing->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
