<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecisionReasonRequest;
use App\Http\Requests\GenerateLecturesRequest;
use App\Http\Requests\LectureRequest;
use App\Models\Lecture;
use App\Models\TrainingGroup;
use App\Services\LectureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LectureController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['group' => ['nullable', 'integer'], 'status' => ['nullable', Rule::in(['scheduled', 'completed', 'cancelled'])], 'q' => ['nullable', 'string', 'max:200']]);
        $area = $request->routeIs('admin.*') ? 'admin' : ($request->routeIs('instructor.*') ? 'instructor' : 'student');
        $groups = TrainingGroup::query()->with('course');
        if ($area === 'instructor') {
            $groups->where('instructor_user_id', $request->user()->id);
        } elseif ($area === 'student') {
            $groups->whereHas('applications', fn ($q) => $q->where('student_user_id', $request->user()->id)->whereHas('enrollment', fn ($q) => $q->where('status', 'active')));
        }
        $groupIds = (clone $groups)->select('training_groups.id');
        $lectures = Lecture::with(['trainingGroup.course', 'trainingGroup.instructor.user'])->whereIn('training_group_id', $groupIds)
            ->when($request->filled('group'), fn ($q) => $q->where('training_group_id', $request->integer('group')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', '%'.$request->input('q').'%')->orWhereHas('trainingGroup', fn ($q) => $q->where('code', 'like', '%'.$request->input('q').'%'))))
            ->orderBy('starts_at')->paginate(15)->withQueryString();

        return view('lectures.index', ['lectures' => $lectures, 'groups' => $groups->orderBy('code')->get(), 'area' => $area]);
    }

    public function show(Lecture $lecture)
    {
        $lecture->load(['trainingGroup.course', 'trainingGroup.instructor.user']);
        Gate::authorize('view', $lecture);

        return view('lectures.show', compact('lecture'));
    }

    public function create()
    {
        Gate::authorize('create', Lecture::class);

        return view('admin.lectures.form', ['lecture' => new Lecture, 'groups' => TrainingGroup::with('course')->orderBy('code')->get()]);
    }

    public function store(LectureRequest $request, LectureService $service)
    {
        $lecture = $service->create($request->user(), TrainingGroup::findOrFail($request->validated('training_group_id')), $request->validated());

        return to_route('lectures.show', $lecture)->setStatusCode(303)->with('status', 'أُضيفت المحاضرة.');
    }

    public function edit(Lecture $lecture)
    {
        Gate::authorize('update', $lecture);
        abort_unless($lecture->status === 'scheduled', 409, 'المحاضرة المكتملة أو الملغاة لا تقبل إعادة الجدولة.');

        return view('admin.lectures.form', ['lecture' => $lecture->load('trainingGroup.course'), 'groups' => TrainingGroup::with('course')->orderBy('code')->get()]);
    }

    public function update(LectureRequest $request, Lecture $lecture, LectureService $service)
    {
        $service->change($request->user(), $lecture, $request->validated());

        return to_route('lectures.show', $lecture)->setStatusCode(303)->with('status', 'حُفظ موعد المحاضرة وسبب التغيير.');
    }

    public function generate()
    {
        Gate::authorize('create', Lecture::class);

        return view('admin.lectures.generate', ['groups' => TrainingGroup::with('course')->withCount('lectures')->orderBy('code')->get()]);
    }

    public function generateStore(GenerateLecturesRequest $request, LectureService $service)
    {
        $service->generate($request->user(), TrainingGroup::findOrFail($request->validated('training_group_id')), $request->validated());

        return to_route('admin.lectures.index', ['group' => $request->validated('training_group_id')])->setStatusCode(303)->with('status', 'وُلّد جدول المحاضرات بنجاح.');
    }

    public function cancel(DecisionReasonRequest $request, Lecture $lecture, LectureService $service)
    {
        $service->change($request->user(), $lecture, $request->validated(), 'cancel');

        return to_route('lectures.show', $lecture)->setStatusCode(303)->with('status', 'أُلغيت المحاضرة وحُفظ السبب.');
    }

    public function complete(Request $request, Lecture $lecture, LectureService $service)
    {
        $service->change($request->user(), $lecture, [], 'complete');

        return to_route('lectures.show', $lecture)->setStatusCode(303)->with('status', 'حُولت المحاضرة إلى مكتملة.');
    }
}
