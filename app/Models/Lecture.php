<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lecture extends Model
{
    protected $fillable = ['training_group_id', 'lecture_number', 'title', 'starts_at', 'ends_at', 'status', 'change_reason'];

    protected function casts(): array
    {
        return ['lecture_number' => 'integer', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    public function trainingGroup(): BelongsTo
    {
        return $this->belongsTo(TrainingGroup::class);
    }
}
