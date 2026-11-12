<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-catalog');
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120', Rule::unique('course_categories')->ignore($this->route('course_category')?->id)], 'slug' => ['nullable', 'string', 'max:150', 'regex:/^[\pL\pN]+(?:-[\pL\pN]+)*$/u', Rule::unique('course_categories')->ignore($this->route('course_category')?->id)]];
    }
}
