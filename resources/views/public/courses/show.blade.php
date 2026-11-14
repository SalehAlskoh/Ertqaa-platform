<x-layout :title="$course->title">
    <x-breadcrumbs :items="[['label'=>'الكورسات','url'=>route('courses.index')],['label'=>$course->title]]" />
    <section class="panel public-hero" aria-labelledby="course-title">
        <div class="public-hero-copy">
            <p class="eyebrow">{{ $course->category?->name }}</p>
            <h1 id="course-title">{{ $course->title }}</h1>
            <p>تعرّف على محتوى الكورس واطّلع على المجموعات التدريبية المنشورة له.</p>
        </div>
        <div class="public-hero-side"><x-icon name="users" :size="25" /><strong>{{ $groups->total() }}</strong><span>مجموعة منشورة لهذا الكورس</span></div>
    </section>

    <div class="public-course-detail-grid">
        <div class="public-course-sections">
            <section class="panel">
                <h2 class="panel-heading"><x-icon name="book" :size="19" />عن الكورس</h2>
                <div class="panel-body"><p class="detail-prose">{{ $course->description ?: 'لا توجد تفاصيل إضافية لهذا الكورس حاليًا.' }}</p></div>
            </section>
            <section aria-labelledby="course-groups-title">
                <div class="public-results-bar !mt-0"><h2 id="course-groups-title">المجموعات المتاحة <span class="result-count">{{ $groups->total() }}</span></h2></div>
                <div class="catalog-list">
                    @forelse($groups as $group)
                        <x-group-row :group="$group" :course="$course" />
                    @empty
                        <x-empty title="لا توجد مجموعات منشورة لهذا الكورس" description="عُد لاحقًا للاطلاع على المجموعات التدريبية الجديدة عند إتاحتها للعرض." />
                    @endforelse
                    @if($groups->hasPages())
                        <div class="pagination">{{ $groups->links() }}</div>
                    @endif
                </div>
            </section>
        </div>
        <aside class="panel public-summary-panel" aria-label="ملخص الكورس">
            <h2 class="panel-heading"><x-icon name="info" :size="19" />نظرة على الكورس</h2>
            <div class="panel-body">
                <dl class="detail-list">
                    <div><dt>التصنيف</dt><dd>{{ $course->category?->name }}</dd></div>
                    <div><dt>الساعات المخططة</dt><dd>{{ $course->planned_hours !== null ? (float)$course->planned_hours.' ساعة' : 'غير محددة' }}</dd></div>
                    <div><dt>المجموعات المنشورة</dt><dd>{{ $groups->total() }} مجموعة</dd></div>
                </dl>
                <a class="button button-secondary" href="{{ route('groups.index') }}">جميع المجموعات <x-icon name="arrow-left" :size="17" /></a>
                <p class="field-help">تختلف مواعيد التسجيل والمحاضر والسعة من مجموعة إلى أخرى.</p>
            </div>
        </aside>
    </div>
</x-layout>
