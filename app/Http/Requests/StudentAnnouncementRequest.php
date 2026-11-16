<?php

namespace App\Http\Requests;

use App\Models\StudentAnnouncement;
use App\Rules\FitsTextColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $announcement = $this->route('studentAnnouncement');

        return $announcement ? $this->user()->can('update', $announcement) : $this->user()->can('create', StudentAnnouncement::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', new FitsTextColumn],
            'target_type' => ['required', Rule::in(['course', 'group'])],
            'course_id' => ['nullable', 'integer', 'exists:courses,id', 'required_if:target_type,course', Rule::prohibitedIf($this->input('target_type') !== 'course')],
            'training_group_id' => ['nullable', 'integer', 'exists:training_groups,id', 'required_if:target_type,group', Rule::prohibitedIf($this->input('target_type') !== 'group')],
            'author_user_id' => ['prohibited'], 'published_at' => ['prohibited'], 'status' => ['prohibited'],
        ];
    }
}
