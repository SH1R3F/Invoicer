<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
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
            'user_id'      => ['required', 'numeric', 'exists:users,id'],
            'quote_date'   => ['required', 'date'],
            'due_date'     => ['required', 'date', 'after_or_equal:quote_date'],

            'products'                    => ['required', 'array'],
            'products.*'                  => ['required', 'array'],
            'products.*.product_id'       => ['nullable', 'numeric', 'exists:products,id'],
            'products.*.product_name'     => ['required', 'string', 'max:255'],
            'products.*.product_price'    => ['required', 'numeric'],
            'products.*.product_quantity' => ['required', 'numeric', 'min:1'],
            'products.*.product_taxes'    => ['nullable', 'array'],

            'discount_type'  => ['nullable', 'string', 'in:fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'required_with:discount_type'],
            'notes'          => ['nullable', 'string']
        ];
    }
}
