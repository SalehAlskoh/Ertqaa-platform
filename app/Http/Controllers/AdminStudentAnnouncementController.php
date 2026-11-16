<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentAnnouncementRequest;
use App\Models\Course;
use App\Models\StudentAnnouncement;
use App\Models\TrainingGroup;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AdminStudentAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', StudentAnnouncement::class);
        $request->validate(['q' => ['nullable', 'string', 'max:200'], 'status' => ['nullable', Rule::in(['draft', 'published', 'scheduled'])], 'target_type' => ['nullable', Rule::in(['course', 'group'])]]);

        return view('admin.student-announcements.index', ['announcements' => StudentAnnouncement::with(['author', 'course', 'trainingGroup.course'])
            ->publicationStatus($request->input('status'))->when($request->input('target_type') === 'course', fn ($q) => $q->whereNotNull('course_id'))
            ->when($request->input('target_type') === 'group', fn ($q) => $q->whereNotNull('training_group_id'))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%')->orWhere('body', 'like', '%'.$request->string('q').'%')))
            ->latest('id')->paginate(15)->withQueryString()]);
    }

    private function form(StudentAnnouncement $announcement)
    {
        return view('admin.student-announcements.form', ['announcement' => $announcement,
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'groups' => TrainingGroup::with('course:id,title')->orderBy('code')->get(['id', 'code', 'course_id'])]);
    }

    public function create()
    {
        Gate::authorize('create', StudentAnnouncement::class);

        return $this->form(new StudentAnnouncement);
    }

    public function store(StudentAnnouncementRequest $request, AnnouncementService $service)
    {
        $announcement = $service->saveStudent($request->user(), $request->validated());

        return to_route('admin.student-announcements.show', $announcement)->setStatusCode(303)->with('status', 'حُفظ إعلان الطلاب كمسودة. يمكنك مراجعته ثم نشره.');
    }

    public function show(StudentAnnouncement $studentAnnouncement)
    {
        Gate::authorize('view', $studentAnnouncement);

        return view('admin.student-announcements.show', ['announcement' => $studentAnnouncement->load(['author', 'course', 'trainingGroup.course'])]);
    }

    public function edit(StudentAnnouncement $studentAnnouncement)
    {
        Gate::authorize('update', $studentAnnouncement);

        return $this->form($studentAnnouncement->load(['author', 'course', 'trainingGroup.course']));
    }

    public function update(StudentAnnouncementRequest $request, StudentAnnouncement $studentAnnouncement, AnnouncementService $service)
    {
        $service->saveStudent($request->user(), $request->validated(), $studentAnnouncement);

        return to_route('admin.student-announcements.show', $studentAnnouncement)->setStatusCode(303)->with('status', 'حُفظت التعديلات دون تغيير حالة النشر أو إرسال إشعار نشر جديد.');
    }

    public function publish(Request $request, StudentAnnouncement $studentAnnouncement, AnnouncementService $service)
    {
        $service->publishStudent($request->user(), $studentAnnouncement);

        return to_route('admin.student-announcements.show', $studentAnnouncement)->setStatusCode(303)->with('status', 'إعلان الطلاب منشور. يُرسل إشعار عند انتقال فعلي إلى النشر فقط.');
    }

    public function unpublish(Request $request, StudentAnnouncement $studentAnnouncement, AnnouncementService $service)
    {
        $service->unpublishStudent($request->user(), $studentAnnouncement);

        return to_route('admin.student-announcements.show', $studentAnnouncement)->setStatusCode(303)->with('status', 'أُلغي نشر الإعلان وحُفظ سجله دون حذف الإشعارات السابقة.');
    }
}
