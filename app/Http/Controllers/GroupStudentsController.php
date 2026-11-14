<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\TrainingGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupStudentsController extends Controller
{
    public function index(Request $request, TrainingGroup $trainingGroup)
    {
        Gate::authorize('viewStudents', $trainingGroup);
        $instructorArea = $request->routeIs('instructor.*');
        if ($instructorArea) {
            abort_unless($request->user()->hasRole('instructor') && $trainingGroup->instructor_user_id === $request->user()->id, 403);
        }
        $enrollments = Enrollment::whereHas('application', fn ($q) => $q->where('training_group_id', $trainingGroup->id))->when($instructorArea, fn ($q) => $q->where('status', 'active'))->with('application.student')->orderBy('enrolled_at')->paginate(15);

        return view('instructor.groups.students', ['trainingGroup' => $trainingGroup->load('course'), 'enrollments' => $enrollments, 'area' => $instructorArea ? 'instructor' : 'admin']);
    }
}
