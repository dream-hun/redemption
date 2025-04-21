<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'domain_name' => 'required|string',
            'registrant_contact_id' => 'required|exists:contacts,id',
            'admin_contact_id' => 'required|exists:contacts,id',
            'tech_contact_id' => 'required|exists:contacts,id',
            'billing_contact_id' => 'required|exists:contacts,id',
            'nameservers' => 'nullable|array|min:2|max:4',
            'nameservers.*' => 'nullable|string|regex:/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'registrant_contact_id.required' => 'A registrant contact is required',
            'admin_contact_id.required' => 'An administrative contact is required',
            'tech_contact_id.required' => 'A technical contact is required',
            'billing_contact_id.required' => 'A billing contact is required',
            'nameservers.min' => 'At least 2 nameservers are required',
            'nameservers.max' => 'Maximum 4 nameservers are allowed',
            'nameservers.*.regex' => 'The nameserver :input is not a valid hostname',
        ];
    }
}
