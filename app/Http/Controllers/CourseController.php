<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Support\UniqueSlug;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function index(Request $r)
    {
        $courses = Course::with('category')->withCount('trainingGroups')->when($r->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$r->string('q').'%'))->when($r->filled('category'), fn ($q) => $q->where('category_id', $r->input('category')))->when(in_array($r->input('published'), ['0', '1'], true), fn ($q) => $q->where('is_published', $r->input('published')))->latest()->paginate(15)->withQueryString();

        return view('admin.courses.index', ['courses' => $courses, 'categories' => CourseCategory::orderBy('name')->get()]);
    }

    public function create()
    {
        Gate::authorize('create', Course::class);

        return view('admin.courses.form', ['course' => new Course, 'categories' => CourseCategory::orderBy('name')->get()]);
    }

    public function edit(Course $course)
    {
        Gate::authorize('update', $course);

        return view('admin.courses.form', compact('course') + ['categories' => CourseCategory::orderBy('name')->get()]);
    }

    public function show(Course $course)
    {
        Gate::authorize('view', $course);
        $course->load('category');

        return view('admin.courses.show', compact('course') + ['groups' => $course->trainingGroups()->with('instructor.user')->latest()->paginate(15)]);
    }

    public function store(CourseRequest $r)
    {
        $d = $r->validated();
        $d['slug'] = $d['slug'] ?? UniqueSlug::make(Course::class, $d['title'], 220);
        $course = Course::create($d);

        return to_route('admin.courses.show', $course)->setStatusCode(303)->with('status', 'تم إنشاء الكورس.');
    }

    public function update(CourseRequest $r, Course $course)
    {
        Gate::authorize('update', $course);
        $d = $r->validated();
        $d['slug'] = $d['slug'] ?? $course->slug;
        DB::transaction(function () use ($course, $d) {
            Course::whereKey($course->id)->lockForUpdate()->firstOrFail()->update($d);
        });

        return to_route('admin.courses.show', $course)->setStatusCode(303)->with('status', 'تم تحديث الكورس.');
    }

    public function publication(Request $r, Course $course)
    {
        Gate::authorize('update', $course);
        $d = $r->validate(['is_published' => ['required', 'boolean']]);
        DB::transaction(fn () => Course::whereKey($course->id)->lockForUpdate()->firstOrFail()->update($d));

        return back()->setStatusCode(303)->with('status', 'تم تحديث حالة النشر.');
    }

    public function destroy(Course $course)
    {
        Gate::authorize('delete', $course);
        try {
            $course->delete();
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? 0) !== 1451) {
                throw $e;
            }

            return back()->setStatusCode(303)->withErrors(['course' => 'الكورس مرتبط ببيانات ولا يمكن حذفه.']);
        }

        return to_route('admin.courses.index')->setStatusCode(303)->with('status', 'تم حذف الكورس غير المرتبط.');
    }
}
