<?php

namespace App\Http\Requests;

use App\Rules\FitsTextColumn;
use Illuminate\Foundation\Http\FormRequest;

class LectureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin', 'staff');
    }

    public function rules(): array
    {
        return [
            'training_group_id' => [$this->route('lecture') ? 'prohibited' : 'required', 'integer', 'exists:training_groups,id'],
            'title' => ['nullable', 'string', 'max:200'],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'ends_at' => ['required', 'date_format:Y-m-d\TH:i', 'after:starts_at'],
            'change_reason' => [$this->route('lecture') ? 'required' : 'nullable', 'string', 'max:65000', new FitsTextColumn],
            'lecture_number' => ['prohibited'], 'status' => ['prohibited'],
        ];
    }
}
