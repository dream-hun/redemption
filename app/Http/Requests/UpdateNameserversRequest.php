<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class UpdateNameserversRequest extends FormRequest
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
            'nameservers' => ['required', 'array', 'min:2', 'max:13'],
            'nameservers.*' => ['required', 'string', 'regex:/^(?=.{1,253}$)(([a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)$/i'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nameservers.required' => 'At least two nameservers are required.',
            'nameservers.min' => 'At least two nameservers are required.',
            'nameservers.max' => 'A maximum of 13 nameservers are allowed.',
            'nameservers.*.required' => 'Each nameserver must have a valid hostname.',
            'nameservers.*.regex' => 'The nameserver must be a valid hostname.',
        ];
    }
}
