<?php

namespace App\Http\Requests;

use App\Models\EnrollmentApplication;
use Illuminate\Foundation\Http\FormRequest;

class ApplyToGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', EnrollmentApplication::class);
    }

    public function rules(): array
    {
        return ['student_user_id' => ['prohibited'], 'status' => ['prohibited'], 'training_group_id' => ['prohibited'], 'reviewed_by_user_id' => ['prohibited'], 'offer_expires_at' => ['prohibited']];
    }
}
