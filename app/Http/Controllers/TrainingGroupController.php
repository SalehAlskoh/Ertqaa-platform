<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingGroupRequest;
use App\Models\Course;
use App\Models\InstructorProfile;
use App\Models\TrainingGroup;
use App\Services\TrainingGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TrainingGroupController extends Controller
{
    public function index(Request $r)
    {
        return view('admin.groups.index', ['groups' => TrainingGroup::with(['course', 'instructor.user'])->when($r->filled('q'), fn ($q) => $q->where('code', 'like', '%'.$r->string('q').'%'))->when($r->filled('course'), fn ($q) => $q->where('course_id', $r->input('course')))->when($r->filled('status'), fn ($q) => $q->where('status', $r->input('status')))->latest()->paginate(15)->withQueryString(), 'courses' => Course::orderBy('title')->get()]);
    }

    private function form(TrainingGroup $trainingGroup)
    {
        return view('admin.groups.form', compact('trainingGroup') + ['courses' => Course::orderBy('title')->get(), 'instructors' => InstructorProfile::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true)->whereHas('roles', fn ($r) => $r->where('name', 'instructor')))->get()]);
    }

    public function create()
    {
        Gate::authorize('create', TrainingGroup::class);

        return $this->form(new TrainingGroup(['status' => 'draft']));
    }

    public function edit(TrainingGroup $training_group)
    {
        Gate::authorize('update', $training_group);

        return $this->form($training_group);
    }

    public function show(TrainingGroup $training_group, TrainingGroupService $service)
    {
        Gate::authorize('view', $training_group);
        [$occupiedSeats,$reservedSeats] = $service->occupancy($training_group);

        return view('admin.groups.show', ['trainingGroup' => $training_group->load(['course.category', 'instructor.user'])] + compact('occupiedSeats', 'reservedSeats'));
    }

    public function store(TrainingGroupRequest $r, TrainingGroupService $service)
    {
        $group = $service->save($r->validated() + ['fee_amount' => null, 'currency_code' => null]);

        return to_route('admin.training-groups.show', $group)->setStatusCode(303)->with('status', 'تم إنشاء المجموعة.');
    }

    public function update(TrainingGroupRequest $r, TrainingGroup $training_group, TrainingGroupService $service)
    {
        Gate::authorize('update', $training_group);
        $service->save($r->validated() + ['fee_amount' => null, 'currency_code' => null], $training_group);

        return to_route('admin.training-groups.show', $training_group)->setStatusCode(303)->with('status', 'تم تحديث المجموعة.');
    }
}
