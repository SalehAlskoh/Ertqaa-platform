<?php

namespace App\Http\Controllers;

use App\Models\TrainingGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InstructorAreaController extends Controller
{
    public function index(Request $r)
    {
        return view('instructor.groups.index', ['groups' => TrainingGroup::where('instructor_user_id', $r->user()->id)->with(['course', 'instructor.user'])->latest()->paginate(15)]);
    }

    public function show(TrainingGroup $trainingGroup)
    {
        Gate::authorize('view', $trainingGroup);

        return view('instructor.groups.show', ['trainingGroup' => $trainingGroup->load(['course.category', 'instructor.user'])]);
    }

    public function profile(Request $r)
    {
        return view('instructor.profile', ['instructor' => $r->user()->instructorProfile()->with('user')->firstOrFail()]);
    }
}
