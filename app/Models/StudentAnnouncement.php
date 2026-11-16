<?php

namespace App\Models;

use App\Models\Concerns\HasPublicationState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnnouncement extends Model
{
    use HasPublicationState;

    protected $fillable = ['title', 'body', 'course_id', 'training_group_id'];

    public function scopeVisibleToStudent(Builder $query, User $user): Builder
    {
        if (! $user->hasRole('student')) {
            return $query->whereRaw('1 = 0');
        }

        $activeStudent = fn ($q) => $q->where('student_user_id', $user->id)->whereHas('enrollment', fn ($q) => $q->where('status', 'active'));

        return $query->published()->where(fn ($q) => $q->whereHas('course.trainingGroups.applications', $activeStudent)
            ->orWhereHas('trainingGroup.applications', $activeStudent));
    }

    public function scopeVisibleToInstructor(Builder $query, User $user): Builder
    {
        if (! $user->hasRole('instructor')) {
            return $query->whereRaw('1 = 0');
        }

        $assigned = fn ($q) => $q->where('instructor_user_id', $user->id);

        return $query->published()->where(fn ($q) => $q->whereHas('course.trainingGroups', $assigned)->orWhereHas('trainingGroup', $assigned));
    }

    /** Current eligible recipients; a notification snapshot never grants future access. */
    public function eligibleStudents(): Builder
    {
        return User::where('is_active', true)->whereHas('roles', fn ($q) => $q->where('name', 'student'))
            ->whereHas('applications', function ($q) {
                $q->whereHas('enrollment', fn ($q) => $q->where('status', 'active'));
                if ($this->course_id !== null) {
                    $q->whereHas('trainingGroup', fn ($q) => $q->where('course_id', $this->course_id));
                } else {
                    $q->where('training_group_id', $this->training_group_id);
                }
            });
    }

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function trainingGroup(): BelongsTo
    {
        return $this->belongsTo(TrainingGroup::class);
    }
}
