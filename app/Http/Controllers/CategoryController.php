<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\CourseCategory;
use App\Support\UniqueSlug;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => CourseCategory::withCount('courses')->orderBy('name')->paginate(15)]);
    }

    public function create()
    {
        Gate::authorize('create', CourseCategory::class);

        return view('admin.categories.form', ['category' => new CourseCategory]);
    }

    public function edit(CourseCategory $course_category)
    {
        Gate::authorize('update', $course_category);

        return view('admin.categories.form', ['category' => $course_category]);
    }

    public function store(CategoryRequest $r)
    {
        $d = $r->validated();
        $d['slug'] = $d['slug'] ?? UniqueSlug::make(CourseCategory::class, $d['name'], 150);
        CourseCategory::create($d);

        return to_route('admin.course-categories.index')->setStatusCode(303)->with('status', 'تم إضافة التصنيف.');
    }

    public function update(CategoryRequest $r, CourseCategory $course_category)
    {
        Gate::authorize('update', $course_category);
        $d = $r->validated();
        $d['slug'] = $d['slug'] ?? $course_category->slug;
        $course_category->update($d);

        return to_route('admin.course-categories.index')->setStatusCode(303)->with('status', 'تم تحديث التصنيف.');
    }

    public function destroy(CourseCategory $course_category)
    {
        Gate::authorize('delete', $course_category);
        try {
            $course_category->delete();
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? 0) !== 1451) {
                throw $e;
            }

            return back()->setStatusCode(303)->withErrors(['category' => 'التصنيف مرتبط بكورسات ولا يمكن حذفه.']);
        }

        return back()->setStatusCode(303)->with('status', 'تم حذف التصنيف غير المرتبط.');
    }
}
