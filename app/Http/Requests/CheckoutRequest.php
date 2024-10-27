<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product.price' => 'required|numeric|min:1',
            'product.quantity' => 'required|numeric|min:0',
            'users.*.balance' => 'required|numeric|min:0',
            'users.*.quantity' => 'required|numeric|min:0'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product.price' => "Price must be at least 1$.",
            'product.quantity' => "Quantity must be zero or greater.",
            'users.*.balance' => "User's balance must be zero or greater.",
            'users.*.quantity' => "Quantity in user's basket must be zero or greater."
        ];
    }
}
