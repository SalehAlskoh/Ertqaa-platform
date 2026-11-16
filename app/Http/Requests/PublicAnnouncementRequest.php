<?php

namespace App\Http\Requests;

use App\Models\PublicAnnouncement;
use App\Rules\FitsTextColumn;
use Illuminate\Foundation\Http\FormRequest;

class PublicAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $announcement = $this->route('publicAnnouncement');

        return $announcement ? $this->user()->can('update', $announcement) : $this->user()->can('create', PublicAnnouncement::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', new FitsTextColumn],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'author_user_id' => ['prohibited'], 'published_at' => ['prohibited'], 'status' => ['prohibited'], 'image_path' => ['prohibited'],
        ];
    }
}
