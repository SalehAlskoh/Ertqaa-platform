<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateManagedAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-accounts');
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => mb_strtolower(trim((string) $this->email))]);
    }

    public function rules(): array
    {
        $new = $this->input('account_mode') === 'new';

        return ['account_mode' => ['required', Rule::in(['new', 'existing'])], 'email' => ['required', 'email', 'max:255', $new ? 'unique:users,email' : 'exists:users,email'],
            'name' => [Rule::excludeIf(! $new), 'required', 'string', 'max:150'], 'password' => [Rule::excludeIf(! $new), 'required', 'confirmed', Password::min(12)],
            'confirm_existing' => [Rule::excludeIf($new), 'accepted'], 'professional_title' => ['nullable', 'string', 'max:150'], 'biography' => ['nullable', 'string', 'max:65000'], 'experience_years' => ['nullable', 'integer', 'min:0', 'max:65535']];
    }
}
