<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VendorRegistrationHeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return  [
            'title' => 'required|string|max:51',
            'sub_title' => 'required|string|max:161',
            'image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }
}
