<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicAnnouncement extends Model
{
    use HasPublicationState;

    protected $fillable = ['title', 'body'];

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_path;
        if (! is_string($path) || ! str_starts_with($path, 'announcements/') || str_contains($path, '..') || str_contains($path, '\\')) {
            return null;
        }

        return asset('storage/'.$path);
    }

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
