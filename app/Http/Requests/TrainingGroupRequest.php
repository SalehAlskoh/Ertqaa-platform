<?php

namespace App\Http\Requests;

use App\Models\TrainingGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrainingGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-catalog');
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'], 'instructor_user_id' => ['required', 'integer', 'exists:instructor_profiles,user_id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('training_groups')->ignore($this->route('training_group')?->id)],
            'capacity' => ['required', 'integer', 'min:1', 'max:65535'], 'fee_amount' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'currency_code' => ['nullable', Rule::requiredIf(fn () => is_scalar($this->input('fee_amount')) && preg_match('/[1-9]/', (string) $this->input('fee_amount')) === 1), 'string', 'regex:/^[A-Z]{3}$/'],
            'registration_opens_at' => ['required', 'date_format:Y-m-d\TH:i'], 'registration_closes_at' => ['required', 'date_format:Y-m-d\TH:i', 'after:registration_opens_at'],
            'status' => ['required', Rule::in(TrainingGroup::STATUSES)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('currency_code')) && $this->filled('currency_code')) {
            $this->merge(['currency_code' => strtoupper($this->input('currency_code'))]);
        }
    }
}
