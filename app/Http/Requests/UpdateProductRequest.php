<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            "name" => "required|min:3|max:20",
            "actual_price" => "required|numeric",
            "sale_price" => "required|numeric",
            "unit" => "required",
            "more_information" => "nullable",
            "brand_id" => "required|exists:brands,id",
            // 'photo' => 'nullable|mimes:png,jpeg,gif'
        ];
    }
}
