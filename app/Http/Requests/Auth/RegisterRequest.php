<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
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
            'email'    => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[0-9\s\W])/i', 'max:255'],
        ];
    }

    /**
     * Register the request's credentials.
     *
     * @throws ValidationException
     */
    public function register(): User
    {
        $user = User::create(['password' => Hash::make($this->password)] + $this->validated());
        $user->syncRoles(Role::where('name', 'client')->first());

        return $user;
    }
}
