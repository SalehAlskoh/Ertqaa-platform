<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StudentAdministrationController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-accounts');
        $request->validate(['q' => ['nullable', 'string', 'max:200'], 'status' => ['nullable', Rule::in(['active', 'inactive'])]]);
        $students = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->with(['studentProfile', 'roles'])
            ->withCount(['applications', 'applications as active_enrollments_count' => fn ($q) => $q->whereHas('enrollment', fn ($q) => $q->where('status', 'active')),
                'applications as withdrawn_enrollments_count' => fn ($q) => $q->whereHas('enrollment', fn ($q) => $q->where('status', 'withdrawn'))])
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%')->orWhere('email', 'like', '%'.$request->string('q').'%')))
            ->when($request->input('status') === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn ($q) => $q->where('is_active', false))->latest('id')->paginate(15)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function show(User $student)
    {
        Gate::authorize('manage-accounts');
        abort_unless($student->roles()->where('name', 'student')->exists(), 404);

        return view('admin.students.show', ['student' => $student->load(['studentProfile', 'roles']),
            'applications' => $student->applications()->with(['trainingGroup.course', 'enrollment', 'reviewer'])->latest('id')->paginate(10, ['*'], 'applications_page')->withQueryString(),
            'enrollments' => Enrollment::whereHas('application', fn ($q) => $q->where('student_user_id', $student->id))
                ->with(['application.trainingGroup.course', 'application.student', 'confirmedBy'])->latest('enrolled_at')->orderByDesc('id')->paginate(10, ['*'], 'enrollments_page')->withQueryString()]);
    }
}
