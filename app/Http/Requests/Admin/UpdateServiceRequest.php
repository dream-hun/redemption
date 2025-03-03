<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateServiceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('service_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'price' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'featured_image' => [
                'required',
            ],
            'duration' => [
                'required',
                'string',
            ],
        ];
    }
}
