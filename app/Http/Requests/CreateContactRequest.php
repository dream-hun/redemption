<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateContactRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'voice' => 'required|string|max:20',
            'organization' => 'nullable|string|max:255',
            'contact_type' => 'required|in:registrant,admin,tech,billing',
            'street1' => 'nullable|string|max:255',
            'street2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|size:2',
            'fax_number' => 'nullable|string|max:20',
            'fax_ext' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The contact name is required',
            'email.required' => 'The contact email is required',
            'email.email' => 'Please provide a valid email address',
            'voice.required' => 'The contact phone number is required',
            'contact_type.required' => 'The contact type is required',
            'contact_type.in' => 'The contact type must be one of: registrant, admin, tech, billing',
        ];
    }
}
