<?php

namespace App\Http\Requests\Auth\AccountSettings;

use App\Rules\CurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'current_password' => ['required', 'string', 'max:255', new CurrentPassword],
            'new_password'     => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[0-9\s\W])/i', 'max:255'],
        ];
    }
}
