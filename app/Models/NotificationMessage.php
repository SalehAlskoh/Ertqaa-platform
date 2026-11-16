<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationMessage extends Model
{
    protected $fillable = ['type', 'title', 'body'];

    public const UPDATED_AT = null;

    public function getStudentAnnouncementIdAttribute(): ?int
    {
        return preg_match('/\Astudent_announcement:([1-9][0-9]{0,18})\z/', $this->type, $matches) ? (int) $matches[1] : null;
    }

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class);
    }
}
