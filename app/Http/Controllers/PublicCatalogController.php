<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\TrainingGroup;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller
{
    public function courses(Request $r)
    {
        return view('public.courses.index', ['courses' => Course::where('is_published', true)->with('category')->withCount(['trainingGroups' => fn ($q) => $q->where('status', 'published')])->when($r->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$r->string('q').'%'))->when($r->filled('category'), fn ($q) => $q->where('category_id', $r->input('category')))->latest()->paginate(12)->withQueryString(), 'categories' => CourseCategory::whereHas('courses', fn ($q) => $q->where('is_published', true))->orderBy('name')->get()]);
    }

    public function course(Course $course)
    {
        abort_unless($course->is_published, 404);

        return view('public.courses.show', ['course' => $course->load('category'), 'groups' => $course->trainingGroups()->where('status', 'published')->with('instructor.user')->latest()->paginate(12)]);
    }

    public function groups(Request $r)
    {
        return view('public.groups.index', ['groups' => TrainingGroup::where('status', 'published')->whereHas('course', fn ($q) => $q->where('is_published', true))->with(['course.category', 'instructor.user'])->when($r->filled('q'), fn ($q) => $q->where(function ($q) use ($r) {
            $q->where('code', 'like', '%'.$r->string('q').'%')->orWhereHas('course', fn ($c) => $c->where('title', 'like', '%'.$r->string('q').'%'));
        }))->when($r->filled('course'), fn ($q) => $q->where('course_id', $r->input('course')))->latest()->paginate(12)->withQueryString(), 'courses' => Course::where('is_published', true)->orderBy('title')->get()]);
    }

    public function group(TrainingGroup $trainingGroup)
    {
        abort_unless($trainingGroup->status === 'published' && $trainingGroup->course->is_published, 404);

        return view('public.groups.show', ['trainingGroup' => $trainingGroup->load(['course.category', 'instructor.user'])]);
    }
}
