<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreGalleryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('gallery_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'location' => [
                'string',
                'nullable',
            ],
            'featured_image' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
