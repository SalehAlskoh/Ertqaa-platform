<?php

namespace App\Http\Controllers;

use App\Models\PublicAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PublicAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['q' => ['nullable', 'string', 'max:200']]);

        return view('public.announcements.index', ['announcements' => PublicAnnouncement::published()->with('author')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%')->orWhere('body', 'like', '%'.$request->string('q').'%')))
            ->orderByDesc('published_at')->orderByDesc('id')->paginate(12)->withQueryString()]);
    }

    public function show(PublicAnnouncement $publicAnnouncement)
    {
        abort_unless($publicAnnouncement->publication_status === 'published', 404);
        Gate::authorize('view', $publicAnnouncement);

        return view('public.announcements.show', ['announcement' => $publicAnnouncement->load('author')]);
    }
}
