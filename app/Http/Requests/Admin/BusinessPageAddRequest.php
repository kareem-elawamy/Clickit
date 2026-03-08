<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BusinessPageAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|unique:business_pages,title',
            'description' => 'required|string',
            'slug' => 'string|unique:business_pages,slug',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => translate('title_is_required'),
            'description.required' => translate('description_is_required'),
            'slug.unique' => translate('slug_must_be_unique'),
        ];
    }

}
