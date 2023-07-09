<?php

namespace App\Http\Requests;

use App\Rules\UrlOrFile;
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
                'string',
                'max:255',
                Rule::when(
                    request()->isMethod('POST'),
                    Rule::unique('products'),
                    Rule::unique('products')->ignore($this->product),
                )
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', new UrlOrFile],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric']
        ];
    }

    protected function passedValidation()
    {
        if (!$this->hasFile('image')) {
            unset($this->image);
        }
    }
}
