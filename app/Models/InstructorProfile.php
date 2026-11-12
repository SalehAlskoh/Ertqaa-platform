<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorProfile extends Model
{
    protected $fillable = ['professional_title', 'biography', 'experience_years'];

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected function casts(): array
    {
        return ['experience_years' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainingGroups(): HasMany
    {
        return $this->hasMany(TrainingGroup::class, 'instructor_user_id', 'user_id');
    }
}
