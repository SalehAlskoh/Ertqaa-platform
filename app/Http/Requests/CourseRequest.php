<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-catalog');
    }

    public function rules(): array
    {
        return ['category_id' => ['required', 'integer', 'exists:course_categories,id'], 'title' => ['required', 'string', 'max:200'], 'slug' => ['nullable', 'string', 'max:220', 'regex:/^[\pL\pN]+(?:-[\pL\pN]+)*$/u', Rule::unique('courses')->ignore($this->route('course')?->id)], 'description' => ['nullable', 'string', 'max:65000'], 'planned_hours' => ['nullable', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999.99'], 'is_published' => ['required', 'boolean']];
    }
}
