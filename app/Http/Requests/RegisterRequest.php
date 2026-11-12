<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:150'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'phone_number' => ['required', 'string', 'max:30'], 'password' => ['required', 'confirmed', Password::min(12)], 'role' => ['prohibited'], 'roles' => ['prohibited'], 'is_active' => ['prohibited'], 'user_id' => ['prohibited']];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => mb_strtolower(trim((string) $this->email))]);
    }
}
