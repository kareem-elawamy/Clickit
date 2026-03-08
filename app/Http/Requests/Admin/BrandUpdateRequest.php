<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class BrandUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
            'name.0' => [
                'required',Rule::unique('brands', 'name')->ignore($this->route('id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'name.0.required' => translate('the_name_field_is_required'),
            'name.0.unique' => translate('The_brand_has_already_been_taken'),
            'image.mimes' => translate('category_image_must_be_jpg_jpeg_png'),
            'image.max' => translate('category_image_must_not_exceed_2mb'),
        ];
    }

}
