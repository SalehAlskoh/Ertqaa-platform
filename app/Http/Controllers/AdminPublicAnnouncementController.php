<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicAnnouncementRequest;
use App\Models\PublicAnnouncement;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AdminPublicAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', PublicAnnouncement::class);
        $request->validate(['q' => ['nullable', 'string', 'max:200'], 'status' => ['nullable', Rule::in(['draft', 'published', 'scheduled'])]]);

        return view('admin.announcements.index', ['announcements' => PublicAnnouncement::with('author')->publicationStatus($request->input('status'))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%')->orWhere('body', 'like', '%'.$request->string('q').'%')))
            ->latest('id')->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        Gate::authorize('create', PublicAnnouncement::class);

        return view('admin.announcements.form', ['announcement' => new PublicAnnouncement]);
    }

    public function store(PublicAnnouncementRequest $request, AnnouncementService $service)
    {
        $announcement = $service->savePublic($request->user(), $request->validated(), null, $request->file('image'));

        return to_route('admin.announcements.show', $announcement)->setStatusCode(303)->with('status', 'حُفظ الإعلان العام كمسودة. يمكنك مراجعته ثم نشره.');
    }

    public function show(PublicAnnouncement $publicAnnouncement)
    {
        Gate::authorize('view', $publicAnnouncement);

        return view('admin.announcements.show', ['announcement' => $publicAnnouncement->load('author')]);
    }

    public function edit(PublicAnnouncement $publicAnnouncement)
    {
        Gate::authorize('update', $publicAnnouncement);

        return view('admin.announcements.form', ['announcement' => $publicAnnouncement->load('author')]);
    }

    public function update(PublicAnnouncementRequest $request, PublicAnnouncement $publicAnnouncement, AnnouncementService $service)
    {
        $service->savePublic($request->user(), $request->validated(), $publicAnnouncement, $request->file('image'));

        return to_route('admin.announcements.show', $publicAnnouncement)->setStatusCode(303)->with('status', 'حُفظت تعديلات الإعلان دون تغيير حالة نشره.');
    }

    public function publish(Request $request, PublicAnnouncement $publicAnnouncement, AnnouncementService $service)
    {
        $service->publishPublic($request->user(), $publicAnnouncement);

        return to_route('admin.announcements.show', $publicAnnouncement)->setStatusCode(303)->with('status', 'الإعلان العام منشور الآن.');
    }

    public function unpublish(Request $request, PublicAnnouncement $publicAnnouncement, AnnouncementService $service)
    {
        $service->unpublishPublic($request->user(), $publicAnnouncement);

        return to_route('admin.announcements.show', $publicAnnouncement)->setStatusCode(303)->with('status', 'أُلغي نشر الإعلان وحُفظ سجله.');
    }
}
