<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:20|unique:brands,name,' . $this->brand,
            'company' => 'required|min:3|max:20',
            'agent' => 'nullable',
            'photo' => 'nullable',
            'phone_no' => 'nullable',
            'description' => 'nullable',
        ];
    }
}
