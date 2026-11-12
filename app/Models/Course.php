<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = ['category_id', 'title', 'slug', 'description', 'planned_hours', 'is_published'];

    protected function casts(): array
    {
        return ['planned_hours' => 'decimal:2', 'is_published' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function trainingGroups(): HasMany
    {
        return $this->hasMany(TrainingGroup::class);
    }

    public function studentAnnouncements(): HasMany
    {
        return $this->hasMany(StudentAnnouncement::class);
    }
}
