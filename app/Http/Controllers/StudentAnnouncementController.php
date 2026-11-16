<?php

namespace App\Http\Controllers;

use App\Models\StudentAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentAnnouncementController extends Controller
{
    private function announcements(Request $request)
    {
        $query = StudentAnnouncement::with(['author', 'course', 'trainingGroup.course']);

        return $request->routeIs('instructor.*') ? $query->visibleToInstructor($request->user()) : $query->visibleToStudent($request->user());
    }

    public function index(Request $request)
    {
        $request->validate(['q' => ['nullable', 'string', 'max:200']]);
        $announcements = $this->announcements($request)
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%')->orWhere('body', 'like', '%'.$request->string('q').'%')))
            ->orderByDesc('published_at')->orderByDesc('id')->paginate(12)->withQueryString();

        return view('student.announcements.index', ['announcements' => $announcements, 'area' => $request->routeIs('instructor.*') ? 'instructor' : 'student']);
    }

    public function show(Request $request, StudentAnnouncement $studentAnnouncement)
    {
        abort_unless($this->announcements($request)->whereKey($studentAnnouncement->id)->exists(), 403);
        Gate::authorize('view', $studentAnnouncement);

        return view('student.announcements.show', ['announcement' => $studentAnnouncement->load(['author', 'course', 'trainingGroup.course']),
            'area' => $request->routeIs('instructor.*') ? 'instructor' : 'student']);
    }
}
