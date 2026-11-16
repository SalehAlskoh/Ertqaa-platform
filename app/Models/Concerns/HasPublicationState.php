<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasPublicationState
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopePublicationStatus(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            'draft' => $query->whereNull('published_at'),
            'published' => $query->published(),
            'scheduled' => $query->where('published_at', '>', now()),
            default => $query,
        };
    }

    public function getPublicationStatusAttribute(): string
    {
        return $this->published_at === null ? 'draft' : ($this->published_at->isFuture() ? 'scheduled' : 'published');
    }
}
