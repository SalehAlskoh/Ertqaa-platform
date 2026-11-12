<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EnrollmentApplication extends Model
{
    protected $fillable = ['student_user_id', 'training_group_id', 'status', 'reviewed_by_user_id', 'reviewed_at', 'rejection_reason', 'offer_expires_at'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime', 'offer_expires_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function trainingGroup(): BelongsTo
    {
        return $this->belongsTo(TrainingGroup::class);
    }

    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class, 'application_id');
    }
}
