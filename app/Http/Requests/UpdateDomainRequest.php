<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class UpdateDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('domain_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'registrant_contact_id' => ['sometimes', 'string'],
            'admin_contact_id' => ['sometimes', 'string'],
            'tech_contact_id' => ['sometimes', 'string'],
            'billing_contact_id' => ['sometimes', 'string'],
            'remove_admin' => ['sometimes', 'boolean'],
            'remove_tech' => ['sometimes', 'boolean'],
            'remove_billing' => ['sometimes', 'boolean'],
            'auto_renew' => ['sometimes', 'boolean'],
            'whois_privacy' => ['sometimes', 'boolean'],
        ];
    }
}
