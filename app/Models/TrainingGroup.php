<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingGroup extends Model
{
    protected $fillable = ['course_id', 'instructor_user_id', 'code', 'capacity', 'fee_amount', 'currency_code', 'registration_opens_at', 'registration_closes_at', 'status'];

    public const STATUSES = ['draft', 'published', 'in_progress', 'completed', 'cancelled'];

    protected function casts(): array
    {
        return ['capacity' => 'integer', 'fee_amount' => 'decimal:2', 'registration_opens_at' => 'datetime', 'registration_closes_at' => 'datetime'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_user_id', 'user_id');
    }

    public function lectures(): HasMany
    {
        return $this->hasMany(Lecture::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(EnrollmentApplication::class);
    }

    public function studentAnnouncements(): HasMany
    {
        return $this->hasMany(StudentAnnouncement::class);
    }
}
