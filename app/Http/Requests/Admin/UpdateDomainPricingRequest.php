<?php

namespace App\Http\Requests\Admin;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainPricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('domain_pricing_edit');
    }

    public function rules(): array
    {
        return [
            'tld' => [
                'string',
                'required',
                'unique:domain_pricings,tld,'.request()->route('domain_pricing')->id,
            ],
            'register_price' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'transfer_price' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'renew_price' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'grace' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'redemption_price' => [
                'string',
                'required',
            ],
        ];
    }
}
