<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryCountryCodeAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'country_code' => 'required|unique:delivery_country_codes,country_code'
        ];
    }

    public function messages(): array
    {
        return [
            'country_code.required' => translate('the_country_code_field_is_required'),
        ];
    }

}
