<x-layout title="الكورسات">
    <section class="panel public-hero" aria-labelledby="catalog-title">
        <div class="public-hero-copy">
            <p class="eyebrow">دليل التعلّم في ارتقاء</p>
            <h1 id="catalog-title">معرفة تُنمّي مهاراتك.</h1>
            <p>استكشف الكورسات المنشورة، تعرّف على محتواها وساعاتها المخططة، واختر ما يناسب اهتماماتك.</p>
            <div class="public-hero-links"><a class="text-link text-xs" href="{{ route('groups.index') }}">استعرض المجموعات التدريبية <x-icon name="arrow-left" :size="16" /></a></div>
        </div>
        <div class="public-hero-side"><x-icon name="book" :size="26" /><strong>{{ $courses->total() }}</strong><span>كورس منشور يطابق التصفية</span></div>
    </section>

    <section class="filter-panel" aria-label="البحث عن الكورسات">
        <form class="filters" action="{{ route('courses.index') }}" method="get">
            <div class="field search-field">
                <label class="field-label" for="q">ابحث عن كورس</label>
                <div class="input-with-icon"><x-icon name="search" :size="19" /><input class="input" id="q" name="q" value="{{ request('q') }}" placeholder="عنوان الكورس…" maxlength="200"></div>
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
            <div class="filter-actions">
                <button class="button" type="submit"><x-icon name="search" :size="18" />بحث</button>
                @if(request()->filled('q') || request()->filled('category'))
                    <a class="button button-secondary" href="{{ route('courses.index') }}"><x-icon name="refresh" :size="16" />مسح التصفية</a>
                @endif
            </div>
        </form>
    </section>

    <div class="public-results-bar"><h2>الكورسات المتاحة <span class="result-count">{{ $courses->total() }}</span></h2><p>المجموعات والساعات بحسب بيانات كل كورس</p></div>
    <section class="public-course-list" aria-label="نتائج الكورسات">
        @forelse($courses as $course)
            <article class="panel public-course-entry">
                <div class="public-course-icon" aria-hidden="true"><x-icon name="book" :size="28" /></div>
                <div class="public-course-content">
                    <span class="badge">{{ $course->category?->name }}</span>
                    <h3><a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a></h3>
                    <p>{{ \Illuminate\Support\Str::limit($course->description ?: 'تعرّف على تفاصيل هذا الكورس والمجموعات المنشورة له.', 190) }}</p>
                </div>
                <div class="public-course-meta">
                    @if($course->planned_hours !== null)
                        <span class="table-meta"><x-icon name="clock" :size="16" />{{ (float)$course->planned_hours }} ساعة مخططة</span>
                    @endif
                    <span class="table-meta"><x-icon name="users" :size="16" />{{ $course->training_groups_count }} مجموعة منشورة</span>
                    <a class="button button-secondary button-small" href="{{ route('courses.show', $course->slug) }}">تفاصيل الكورس <x-icon name="arrow-left" :size="16" /></a>
                </div>
            </article>
        @empty
            <div class="panel"><x-empty title="لا توجد كورسات منشورة مطابقة" description="جرّب تغيير عنوان البحث أو التصنيف، أو عُد لاحقًا للاطلاع على الكورسات الجديدة." /></div>
        @endforelse
    </section>
    @if($courses->hasPages())
        <div class="panel mt-5"><div class="pagination">{{ $courses->withQueryString()->links() }}</div></div>
    @endif
</x-layout>
