<?php

namespace App\Http\Requests;

use App\Rules\UrlOrImage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'sku' => [
                'nullable',
                'max:255',
                Rule::when(
                    request()->isMethod('POST'),
                    Rule::unique('products'),
                    Rule::unique('products')->ignore($this->product),
                )
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', new UrlOrImage],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric']
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        if (!$this->hasFile('image')) {
            unset($validated['image']);
        }

        return $validated;
    }
}
