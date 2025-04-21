<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDomainPricingRequest;
use App\Http\Requests\Admin\UpdateDomainPricingRequest;
use App\Models\DomainPricing;
use Gate;
use Symfony\Component\HttpFoundation\Response;

final class DomainPricingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('domain_pricing_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domainPricings = DomainPricing::all();

        return view('admin.domainPricings.index', ['domainPricings' => $domainPricings]);
    }

    public function create()
    {
        abort_if(Gate::denies('domain_pricing_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.domainPricings.create');
    }

    public function store(StoreDomainPricingRequest $request)
    {
        DomainPricing::create($request->all());

        return redirect()->route('admin.domain-pricings.index');
    }

    public function edit(DomainPricing $domainPricing)
    {
        abort_if(Gate::denies('domain_pricing_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.domainPricings.edit', ['domainPricing' => $domainPricing]);
    }

    public function update(UpdateDomainPricingRequest $request, DomainPricing $domainPricing)
    {
        $domainPricing->update($request->all());

        return redirect()->route('admin.domain-pricings.index')->withSuccess('Domain Pricing updated successfully');
    }

    public function destroy(DomainPricing $domainPricing)
    {
        abort_if(Gate::denies('domain_pricing_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domainPricing->delete();

        return back();
    }
}
