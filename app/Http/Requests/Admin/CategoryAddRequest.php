<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $icon
 * @property int $parent_id
 * @property int $position
 * @property int $home_status
 * @property int $priority
 */
class CategoryAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:2048',
            'priority' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('category_name_is_required'),
            'image.required' => translate('category_image_is_required'),
            'image.mimes' => translate('category_image_must_be_jpg_jpeg_png'),
            'image.max' => translate('category_image_must_not_exceed_2mb'),
            'priority.required' => translate('category_priority_is_required'),
        ];
    }

}
