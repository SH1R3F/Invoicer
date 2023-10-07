<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                Rule::when(
                    request()->isMethod('POST'),
                    Rule::unique('taxes'),
                    Rule::unique('taxes')->ignore($this->tax)
                )
            ],
            'value'   => ['required', 'numeric'],
            'type'    => ['required', 'string', 'in:fixed,percentage'],
            'default' => ['nullable', 'boolean']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'default' => $this->boolean($this->default)
        ]);
    }
}
