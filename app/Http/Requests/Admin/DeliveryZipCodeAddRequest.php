<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryZipCodeAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'zipcode' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'zipcode.required' => translate('the_zipcode_field_is_required'),
        ];
    }

}
