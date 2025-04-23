<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class DomainTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'auth_code' => 'required|string',
            'new_registrant_id' => ['required', 'exists:contacts,contact_id'],
            // 'new_registrant_email' => 'required|email',
            // 'new_registrant_name' => 'required|string',
            // 'new_registrant_org' => 'nullable|string',
            // 'new_registrant_phone' => 'required|string',
            // 'new_registrant_address' => 'required|string',
            // 'new_registrant_city' => 'required|string',
            // 'new_registrant_country' => 'required|string|size:2',
        ];
    }
}
