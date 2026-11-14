@php
    $pageCourses = $courses->getCollection();
    $publishedOnPage = $pageCourses->where('is_published', true)->count();
    $draftsOnPage = $pageCourses->where('is_published', false)->count();
    $hoursOnPage = number_format((float) $pageCourses->sum('planned_hours'), 2);
@endphp
<x-layout title="إدارة الكورسات">
    <x-page-heading title="إدارة الكورسات" description="عرض الكورسات، تنظيم تصنيفاتها، ومتابعة الساعات المخططة وحالات النشر.">
        <a class="button" href="{{ route('admin.courses.create') }}"><x-icon name="plus" :size="19" />إضافة كورس جديد</a>
    </x-page-heading>

    <section class="stats-grid" aria-label="ملخص نتائج الكورسات">
        <x-stat-card label="الكورسات المطابقة للتصفية" :value="$courses->total()" icon="book" note="بحسب خيارات البحث الحالية" />
        <x-stat-card label="منشورة في هذه الصفحة" :value="$publishedOnPage" icon="check-circle" note="الكورسات الظاهرة في الصفحة فقط" tone="green" />
        <x-stat-card label="مسودات في هذه الصفحة" :value="$draftsOnPage" icon="file" note="الكورسات غير المنشورة في الصفحة" tone="gold" />
        <x-stat-card label="الساعات المخططة في هذه الصفحة" :value="$hoursOnPage" icon="clock" unit="ساعة" note="مجموع الساعات المحددة للصفحة" tone="soft" />
    </section>

    <div class="notice notice-info">
        <x-icon name="info" />
        <p>الكورسات المنشورة تظهر في الدليل العام. إلغاء النشر يخفي الكورس ومجموعاته عن العامة مع الاحتفاظ ببياناتها.</p>
    </div>

    <section class="filter-panel" aria-label="البحث وتصفية الكورسات">
        <form class="filters" action="{{ route('admin.courses.index') }}" method="get">
            <div class="field search-field">
                <label class="field-label" for="q">البحث عن كورس</label>
                <div class="input-with-icon"><x-icon name="search" :size="19" /><input class="input" id="q" name="q" value="{{ request('q') }}" placeholder="ابحث بعنوان الكورس…" maxlength="200"></div>
            </div>
            <div class="field">
                <label class="field-label" for="category">التصنيف</label>
                <select class="input" id="category" name="category">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string)request('category') === (string)$category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="field-label" for="published">حالة النشر</label>
                <select class="input" id="published" name="published">
                    <option value="">جميع الحالات</option>
                    <option value="1" @selected(request('published') === '1')>منشور</option>
                    <option value="0" @selected(request('published') === '0')>غير منشور</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="button"><x-icon name="filter" :size="17" />تصفية</button>
                @if(request()->hasAny(['q','category','published']))
                    <a class="icon-button icon-button-bordered" href="{{ route('admin.courses.index') }}" aria-label="مسح جميع خيارات التصفية" title="مسح التصفية"><x-icon name="refresh" :size="18" /></a>
                @endif
            </div>
        </form>
    </section>

    <section class="panel table-panel" aria-labelledby="courses-table-title">
        <div class="table-panel-heading"><h2 id="courses-table-title">سجل الكورسات <span class="result-count">{{ $courses->total() }}</span></h2><span class="table-scroll-hint"><x-icon name="arrow-left" :size="14" />اسحب أفقيًا لعرض بقية الأعمدة</span></div>
        @if($courses->count())
            <div class="table-scroll" tabindex="0" role="region" aria-label="جدول الكورسات، قابل للتمرير أفقيًا">
                <table class="data-table course-management-table">
                    <caption class="visually-hidden">الكورسات المطابقة لخيارات البحث، مع التصنيف والساعات والمجموعات وحالة النشر والإجراءات.</caption>
                    <thead><tr><th scope="col">المعرّف</th><th scope="col">عنوان الكورس والتصنيف</th><th scope="col">الساعات المخططة</th><th scope="col">المجموعات المرتبطة</th><th scope="col">حالة النشر</th><th scope="col">الإجراءات</th></tr></thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td><span class="table-course-id" dir="ltr">#{{ $course->id }}</span></td>
                                <td class="course-name-cell"><a class="table-primary" href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a><div class="table-secondary">{{ $course->category?->name }}</div></td>
                                <td>
                                    <span class="table-meta"><x-icon name="clock" :size="16" />{{ $course->planned_hours !== null ? (float)$course->planned_hours.' ساعة' : 'غير محددة' }}</span>
                                </td>
                                <td><span class="linked-count"><x-icon name="users" :size="15" />{{ $course->training_groups_count }} مجموعة</span></td>
                                <td><x-badge :status="$course->is_published ? 'published' : 'draft'" /></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="icon-button" href="{{ route('admin.courses.show', $course) }}" aria-label="تفاصيل {{ $course->title }}" title="التفاصيل"><x-icon name="eye" :size="18" /></a>
                                        <a class="icon-button" href="{{ route('admin.courses.edit', $course) }}" aria-label="تعديل {{ $course->title }}" title="تعديل"><x-icon name="edit" :size="17" /></a>
                                        <form action="{{ route('admin.courses.publication', $course) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="{{ $course->is_published ? '0' : '1' }}">
                                            <button class="button button-secondary button-small" type="submit">{{ $course->is_published ? 'إلغاء النشر' : 'نشر' }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-empty title="لا توجد كورسات مطابقة" description="أضف كورسًا جديدًا أو جرّب تغيير خيارات البحث والتصفية."><a class="button button-secondary" href="{{ route('admin.courses.create') }}"><x-icon name="plus" :size="17" />إضافة كورس</a></x-empty>
        @endif
        @if($courses->hasPages())
            <div class="pagination">{{ $courses->withQueryString()->links() }}</div>
        @endif
    </section>
</x-layout>
