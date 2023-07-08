<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'email',
                Rule::when(
                    request()->isMethod('POST'),
                    Rule::unique('users'),
                    Rule::unique('users')->ignore($this->user),
                ),
                'max:255'
            ],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'password' => [
                Rule::when(
                    request()->isMethod('POST'),
                    ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[0-9\s\W])/i', 'max:255'],
                    'nullable'
                )
            ],
        ];
    }
}
