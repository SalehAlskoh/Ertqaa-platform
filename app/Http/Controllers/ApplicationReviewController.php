<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmEnrollmentRequest;
use App\Http\Requests\DecisionReasonRequest;
use App\Models\EnrollmentApplication;
use App\Models\TrainingGroup;
use App\Services\EnrollmentService;
use App\Services\TrainingGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ApplicationReviewController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['group' => ['nullable', 'integer'], 'status' => ['nullable', Rule::in(EnrollmentApplication::STATUSES)], 'q' => ['nullable', 'string', 'max:200']]);
        $applications = EnrollmentApplication::with(['student.studentProfile', 'trainingGroup.course', 'enrollment'])
            ->when($request->filled('group'), fn ($q) => $q->where('training_group_id', $request->integer('group')))
            ->when($request->filled('status'), fn ($q) => $q->effectiveStatus($request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->whereHas('student', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$request->input('q').'%')->orWhere('email', 'like', '%'.$request->input('q').'%'))))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.applications.index', ['applications' => $applications, 'groups' => TrainingGroup::with('course')->orderBy('code')->get()]);
    }

    private function details(EnrollmentApplication $application, string $view, TrainingGroupService $service)
    {
        $application->load(['student.studentProfile', 'trainingGroup.course', 'enrollment.confirmedBy', 'reviewer']);
        [$occupiedSeats, $reservedSeats] = $service->occupancy($application->trainingGroup);

        return view($view, compact('application', 'occupiedSeats', 'reservedSeats'));
    }

    public function show(EnrollmentApplication $application, TrainingGroupService $service)
    {
        Gate::authorize('view', $application);

        return $this->details($application, 'admin.applications.show', $service);
    }

    public function approve(Request $request, EnrollmentApplication $application, EnrollmentService $service)
    {
        $service->approve($request->user(), $application);

        return to_route('admin.applications.show', $application)->setStatusCode(303)->with('status', 'اعتُمد القبول المبدئي؛ تنتهي مهلة الحجز بعد 48 ساعة من الموافقة الأولى.');
    }

    public function reject(DecisionReasonRequest $request, EnrollmentApplication $application, EnrollmentService $service)
    {
        $service->reject($request->user(), $application, $request->validated('rejection_reason'));

        return to_route('admin.applications.show', $application)->setStatusCode(303)->with('status', 'رُفض الطلب وحُفظ السبب.');
    }

    public function confirmation(EnrollmentApplication $application, TrainingGroupService $service)
    {
        Gate::authorize('confirm', $application);
        if ($application->effective_status !== 'awaiting_confirmation') {
            return to_route('admin.applications.show', $application)->withErrors(['application' => 'لا يوجد حجز صالح للتأكيد في الحالة الحالية.']);
        }

        return $this->details($application, 'admin.applications.confirm', $service);
    }

    public function confirm(ConfirmEnrollmentRequest $request, EnrollmentApplication $application, EnrollmentService $service)
    {
        $service->confirm($request->user(), $application, $request->validated('paid_amount'));

        return to_route('admin.applications.show', $application)->setStatusCode(303)->with('status', 'اكتمل التسجيل الحضوري وحُفظ سجل التحصيل.');
    }
}
