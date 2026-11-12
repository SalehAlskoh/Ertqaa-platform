<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRecipient extends Model
{
    protected $fillable = ['notification_message_id', 'user_id', 'read_at'];

    public $timestamps = false;

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(NotificationMessage::class, 'notification_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
