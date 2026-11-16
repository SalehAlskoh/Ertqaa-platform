<?php

namespace App\Services;

use App\Models\PublicAnnouncement;
use App\Models\StudentAnnouncement;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class AnnouncementService
{
    public function __construct(private InternalNotificationService $notifications) {}

    public function savePublic(User $actor, array $data, ?PublicAnnouncement $announcement = null, ?UploadedFile $image = null): PublicAnnouncement
    {
        Gate::forUser($actor)->authorize($announcement ? 'update' : 'create', $announcement ?? PublicAnnouncement::class);
        $imagePath = $image?->store('announcements', 'public');
        if ($image !== null && ! $imagePath) {
            throw ValidationException::withMessages(['image' => 'تعذر حفظ الصورة. تحقق من صلاحية مجلد التخزين ثم أعد المحاولة.']);
        }

        return DB::transaction(function () use ($actor, $data, $announcement, $imagePath) {
            $record = $announcement ? PublicAnnouncement::whereKey($announcement->id)->lockForUpdate()->firstOrFail() : new PublicAnnouncement;
            $record->fill(['title' => $data['title'], 'body' => $data['body']]);
            if (! $record->exists) {
                $record->author_user_id = $actor->id;
            }
            if ($imagePath !== null) {
                $record->image_path = $imagePath;
            }
            $record->save();

            return $record;
        }, 3);
    }

    public function saveStudent(User $actor, array $data, ?StudentAnnouncement $announcement = null): StudentAnnouncement
    {
        Gate::forUser($actor)->authorize($announcement ? 'update' : 'create', $announcement ?? StudentAnnouncement::class);
        $courseId = ! empty($data['course_id']) ? (int) $data['course_id'] : null;
        $groupId = ! empty($data['training_group_id']) ? (int) $data['training_group_id'] : null;
        if (($courseId === null) === ($groupId === null)) {
            throw ValidationException::withMessages(['target_type' => 'اختر كورسًا واحدًا أو مجموعة واحدة فقط للإعلان.']);
        }

        return DB::transaction(function () use ($actor, $data, $announcement, $courseId, $groupId) {
            $record = $announcement ? StudentAnnouncement::whereKey($announcement->id)->lockForUpdate()->firstOrFail() : new StudentAnnouncement;
            $record->fill(['title' => $data['title'], 'body' => $data['body'], 'course_id' => $courseId, 'training_group_id' => $groupId]);
            if (! $record->exists) {
                $record->author_user_id = $actor->id;
            }
            $record->save();

            return $record;
        }, 3);
    }

    public function publishPublic(User $actor, PublicAnnouncement $announcement): PublicAnnouncement
    {
        return $this->publication($actor, $announcement, true);
    }

    public function unpublishPublic(User $actor, PublicAnnouncement $announcement): PublicAnnouncement
    {
        return $this->publication($actor, $announcement, false);
    }

    public function publishStudent(User $actor, StudentAnnouncement $announcement): StudentAnnouncement
    {
        return $this->publication($actor, $announcement, true);
    }

    public function unpublishStudent(User $actor, StudentAnnouncement $announcement): StudentAnnouncement
    {
        return $this->publication($actor, $announcement, false);
    }

    private function publication(User $actor, PublicAnnouncement|StudentAnnouncement $announcement, bool $publish): PublicAnnouncement|StudentAnnouncement
    {
        Gate::forUser($actor)->authorize($publish ? 'publish' : 'unpublish', $announcement);

        return DB::transaction(function () use ($actor, $announcement, $publish) {
            $record = $announcement->newQuery()->whereKey($announcement->id)->lockForUpdate()->firstOrFail();
            $at = now();
            if ($publish && (! $record->published_at || $record->published_at->gt($at))) {
                $record->published_at = $at;
                $record->author_user_id = $actor->id;
                $record->save();
                if ($record instanceof StudentAnnouncement) {
                    // One generic message with a typed identifier; no protected title/body is copied.
                    $this->notifications->send($record->eligibleStudents()->pluck('users.id')->all(), 'student_announcement:'.$record->id,
                        'إعلان جديد للطلاب', 'يتوفر إعلان جديد لتسجيلاتك. افتح الإعلان للتحقق من إمكانية الاطلاع عليه.');
                }
            } elseif (! $publish && $record->published_at !== null) {
                $record->published_at = null;
                $record->save();
            }

            return $record;
        }, 3);
    }
}
