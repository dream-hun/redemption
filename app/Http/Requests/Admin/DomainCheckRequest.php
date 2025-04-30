<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DomainCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'domain_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9\-\.]+$/'],
        ];
    }
}