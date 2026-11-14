<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLecturesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin', 'staff');
    }

    public function rules(): array
    {
        return ['training_group_id' => ['required', 'integer', 'exists:training_groups,id'], 'first_date' => ['required', 'date_format:Y-m-d'], 'weekdays' => ['required', 'array', 'min:1', 'max:7'], 'weekdays.*' => ['required', 'integer', 'between:0,6', 'distinct'], 'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i', 'after:start_time'], 'count' => ['required', 'integer', 'between:1,200']];
    }
}
