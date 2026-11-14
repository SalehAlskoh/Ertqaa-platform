<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyToGroupRequest;
use App\Models\EnrollmentApplication;
use App\Models\TrainingGroup;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentApplicationController extends Controller
{
    public function index(Request $request)
    {
        return view('student.applications.index', ['applications' => $request->user()->applications()->with(['trainingGroup.course', 'enrollment'])->latest()->paginate(15)]);
    }

    public function show(Request $request, EnrollmentApplication $application)
    {
        abort_unless($application->student_user_id === $request->user()->id, 403);
        Gate::authorize('view', $application);

        return view('student.applications.show', ['application' => $application->load(['trainingGroup.course', 'enrollment', 'student', 'reviewer'])]);
    }

    public function store(ApplyToGroupRequest $request, TrainingGroup $trainingGroup, EnrollmentService $service)
    {
        $application = $service->apply($request->user(), $trainingGroup);

        return to_route('student.applications.show', $application)->setStatusCode(303)->with('status', 'أُرسل طلبك للمراجعة؛ الطلب المبدئي لا يحجز مقعدًا.');
    }

    public function cancel(Request $request, EnrollmentApplication $application, EnrollmentService $service)
    {
        $service->cancel($request->user(), $application);

        return to_route('student.applications.show', $application)->setStatusCode(303)->with('status', 'أُلغي الطلب دون حذف سجله.');
    }
}
