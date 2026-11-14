<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('confirm', $this->route('application'));
    }

    public function rules(): array
    {
        return ['paid_amount' => ['required', 'regex:/^\d{1,8}(?:\.\d{1,2})?$/D'], 'confirmed_by_user_id' => ['prohibited'], 'paid_at' => ['prohibited'], 'enrolled_at' => ['prohibited'], 'student_user_id' => ['prohibited'], 'status' => ['prohibited']];
    }
}
