<?php

namespace App\Http\Requests\Auth\AccountSettings;

use App\Rules\UrlOrImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'avatar'   => ['required', new UrlOrImage]
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        if (!$this->hasFile('avatar')) {
            unset($validated['avatar']);
        }

        return $validated;
    }
}
