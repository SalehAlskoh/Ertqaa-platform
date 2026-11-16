<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentApplication;
use App\Models\Lecture;
use App\Models\NotificationRecipient;
use App\Models\TrainingGroup;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class DashboardDataService
{
    private const ROW_LIMIT = 6;

    public function __construct(private TrainingGroupService $groups) {}

    /** Today is a half-open Tripoli calendar day, compared to UTC storage. */
    public function dayWindow(?CarbonImmutable $at = null): array
    {
        $local = ($at ?? CarbonImmutable::now('UTC'))->setTimezone('Africa/Tripoli')->startOfDay();

        return [$local->utc(), $local->addDay()->utc(), $local->format('Y/m/d')];
    }

    private function applications(): Builder
    {
        return EnrollmentApplication::query()->with(['student', 'trainingGroup.course', 'enrollment']);
    }

    private function reservations(CarbonImmutable $at): Builder
    {
        return $this->groups->reservedApplications($this->applications(), $at);
    }

    private function lectures(): Builder
    {
        return Lecture::query()->with(['trainingGroup.course', 'trainingGroup.instructor.user']);
    }

    private function today(CarbonImmutable $start, CarbonImmutable $end): Builder
    {
        return $this->lectures()->where('starts_at', '>=', $start)->where('starts_at', '<', $end);
    }

    private function operational(CarbonImmutable $at): array
    {
        [$start, $end, $date] = $this->dayWindow($at);

        return [
            'localDate' => $date,
            'recentApplications' => $this->applications()->latest('id')->limit(self::ROW_LIMIT)->get(),
            'expiringReservations' => $this->reservations($at)->where('offer_expires_at', '<=', $at->addHours(24))->orderBy('offer_expires_at')->orderBy('id')->limit(self::ROW_LIMIT)->get(),
            'todayLectures' => $this->today($start, $end)->orderBy('starts_at')->orderBy('id')->limit(self::ROW_LIMIT)->get(),
        ];
    }

    public function admin(): array
    {
        $at = CarbonImmutable::now('UTC');
        [$start, $end] = $this->dayWindow($at);
        $statusCounts = TrainingGroup::select('status')->selectRaw('COUNT(*) as total')->groupBy('status')->pluck('total', 'status')->map(fn ($count) => (int) $count)->all();
        $capacityQuery = $this->groups->withOccupancy(TrainingGroup::query(), $at)->whereIn('status', ['published', 'in_progress']);

        return $this->operational($at) + [
            'stats' => [
                'active_students' => User::where('is_active', true)->whereHas('roles', fn ($q) => $q->where('name', 'student'))->count(),
                'published_courses' => Course::where('is_published', true)->count(),
                'groups_total' => TrainingGroup::count(),
                'active_instructors' => User::where('is_active', true)->whereHas('roles', fn ($q) => $q->where('name', 'instructor'))->count(),
                'pending_applications' => EnrollmentApplication::where('status', 'pending')->count(),
                'valid_reservations' => $this->reservations($at)->count(),
                'active_enrollments' => Enrollment::where('status', 'active')->count(),
                'today_scheduled_lectures' => $this->today($start, $end)->where('status', 'scheduled')->count(),
            ],
            'groupStatuses' => array_replace(array_fill_keys(TrainingGroup::STATUSES, 0), $statusCounts),
            'nearCapacityGroups' => TrainingGroup::query()->fromSub($capacityQuery, 'training_groups')->with(['course', 'instructor.user'])
                ->whereRaw('(occupied_seats + reserved_seats) >= capacity * 0.8')
                ->orderByRaw('(occupied_seats + reserved_seats) / capacity DESC')->orderBy('id')->limit(self::ROW_LIMIT)->get(),
            // A bounded historical ledger. No cross-currency total or net-profit claim.
            'recordedPayments' => Enrollment::whereNotNull('paid_amount')->with(['application.student', 'application.trainingGroup.course', 'confirmedBy'])
                ->orderByDesc('enrolled_at')->orderByDesc('id')->limit(self::ROW_LIMIT)->get(),
        ];
    }

    public function staff(): array
    {
        $at = CarbonImmutable::now('UTC');
        [$start, $end] = $this->dayWindow($at);

        return $this->operational($at) + [
            'stats' => [
                'pending_applications' => EnrollmentApplication::where('status', 'pending')->count(),
                'valid_reservations' => $this->reservations($at)->count(),
                'expiring_reservations' => $this->reservations($at)->where('offer_expires_at', '<=', $at->addHours(24))->count(),
                'confirmed_today' => Enrollment::where('enrolled_at', '>=', $start)->where('enrolled_at', '<', $end)->count(),
                'today_scheduled_lectures' => $this->today($start, $end)->where('status', 'scheduled')->count(),
            ],
            'awaitingConfirmation' => $this->reservations($at)->orderBy('offer_expires_at')->orderBy('id')->limit(self::ROW_LIMIT)->get(),
        ];
    }

    private function notifications(User $user)
    {
        return NotificationRecipient::where('user_id', $user->id)->with('message')->latest('id')->limit(5)->get();
    }

    public function instructor(User $user): array
    {
        $at = CarbonImmutable::now('UTC');
        [$start, $end, $date] = $this->dayWindow($at);
        $lectures = $this->lectures()->whereHas('trainingGroup', fn ($q) => $q->where('instructor_user_id', $user->id));
        $upcoming = (clone $lectures)->where('status', 'scheduled')->where('starts_at', '>=', $at);

        return [
            'localDate' => $date,
            'stats' => [
                'assigned_groups' => TrainingGroup::where('instructor_user_id', $user->id)->count(),
                'active_students' => EnrollmentApplication::whereHas('trainingGroup', fn ($q) => $q->where('instructor_user_id', $user->id))
                    ->whereHas('enrollment', fn ($q) => $q->where('status', 'active'))->distinct()->count('student_user_id'),
                'upcoming_lectures' => (clone $upcoming)->count(),
                'today_scheduled_lectures' => (clone $lectures)->where('status', 'scheduled')->where('starts_at', '>=', $start)->where('starts_at', '<', $end)->count(),
            ],
            'myGroups' => $this->groups->withOccupancy(TrainingGroup::with('course')->where('instructor_user_id', $user->id), $at)->latest('id')->limit(self::ROW_LIMIT)->get(),
            'upcomingLectures' => $upcoming->orderBy('starts_at')->orderBy('id')->limit(self::ROW_LIMIT)->get(),
            'notifications' => $this->notifications($user),
        ];
    }

    public function student(User $user): array
    {
        $at = CarbonImmutable::now('UTC');
        [, , $date] = $this->dayWindow($at);
        $applications = $this->applications()->where('student_user_id', $user->id);
        $enrollments = Enrollment::where('status', 'active')->whereHas('application', fn ($q) => $q->where('student_user_id', $user->id));
        $lectures = $this->lectures()->where('status', 'scheduled')->where('starts_at', '>=', $at)
            ->whereHas('trainingGroup.applications', fn ($q) => $q->where('student_user_id', $user->id)->whereHas('enrollment', fn ($q) => $q->where('status', 'active')));

        return [
            'localDate' => $date,
            'stats' => [
                'pending_applications' => (clone $applications)->where('status', 'pending')->count(),
                'valid_reservations' => $this->groups->reservedApplications(clone $applications, $at)->count(),
                'active_enrollments' => (clone $enrollments)->count(),
                'upcoming_lectures' => (clone $lectures)->count(),
            ],
            'myApplications' => $applications->latest('id')->limit(self::ROW_LIMIT)->get(),
            'validOffers' => $this->reservations($at)->where('student_user_id', $user->id)->orderBy('offer_expires_at')->orderBy('id')->limit(self::ROW_LIMIT)->get(),
            'activeEnrollments' => $enrollments->with(['application.trainingGroup.course', 'application.trainingGroup.instructor.user'])->latest('enrolled_at')->orderByDesc('id')->limit(self::ROW_LIMIT)->get(),
            'upcomingLectures' => $lectures->orderBy('starts_at')->orderBy('id')->limit(self::ROW_LIMIT)->get(),
            'notifications' => $this->notifications($user),
        ];
    }
}
