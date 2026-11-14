<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        return view('student.enrollments.index', ['enrollments' => Enrollment::whereHas('application', fn ($q) => $q->where('student_user_id', $request->user()->id))->with('application.trainingGroup.course')->latest()->paginate(15)]);
    }

    public function show(Request $request, Enrollment $enrollment)
    {
        $enrollment->load(['application.trainingGroup.course', 'application.student', 'confirmedBy']);
        abort_unless($enrollment->application->student_user_id === $request->user()->id, 403);
        Gate::authorize('view', $enrollment);

        return view('student.enrollments.show', compact('enrollment'));
    }

    public function withdraw(Request $request, Enrollment $enrollment, EnrollmentService $service)
    {
        $service->withdraw($request->user(), $enrollment);

        return to_route('student.enrollments.show', $enrollment)->setStatusCode(303)->with('status', 'حُفظ انسحابك؛ لا يعني الانسحاب استرداد الرسوم.');
    }
}
