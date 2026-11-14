<x-layout :title="$course->title">
    <x-breadcrumbs :items="[['label'=>'الكورسات','url'=>route('admin.courses.index')],['label'=>$course->title]]" />
    <section class="panel group-hero">
        <div class="group-hero-header">
            <div>
                <div class="group-hero-meta"><x-badge :status="$course->is_published ? 'published' : 'draft'" /><span class="badge"><x-icon name="tag" :size="14" />{{ $course->category?->name }}</span><span class="group-code">المعرّف: <bdi>#{{ $course->id }}</bdi></span></div>
                <h1 class="page-title">{{ $course->title }}</h1>
                <p class="page-description">محتوى الكورس ومجموعاته التدريبية وحالة عرضه للعامة.</p>
            </div>
            <div class="group-hero-actions">
                <a class="button" href="{{ route('admin.courses.edit', $course) }}"><x-icon name="edit" :size="17" />تعديل الكورس</a>
                @if($course->is_published)<a class="button button-secondary" href="{{ route('courses.show', $course->slug) }}"><x-icon name="eye" :size="18" />العرض العام</a>@endif
            </div>
        </div>
    </section>
    <div class="detail-layout">
        <div class="admin-course-body">
            <section class="panel"><h2 class="panel-heading"><x-icon name="book" :size="19" />محتوى الكورس</h2><div class="panel-body"><p class="detail-prose">{{ $course->description ?: 'لم تتم إضافة وصف لهذا الكورس.' }}</p></div></section>
            <section>
                <div class="admin-section-heading"><h2>المجموعات التدريبية <span class="result-count">{{ $groups->total() }}</span></h2><a class="button button-secondary button-small" href="{{ route('admin.training-groups.create', ['course_id' => $course->id]) }}"><x-icon name="plus" :size="16" />إضافة مجموعة</a></div>
                <div class="catalog-list">
                    @forelse($groups as $group)
                        <x-group-row :group="$group" :course="$course" route-name="admin.training-groups.show" />
                    @empty
                        <x-empty title="لا توجد مجموعات لهذا الكورس" description="أضف مجموعة لتحديد المحاضر والسعة وفترة التسجيل." />
                    @endforelse
                    @if($groups->hasPages())<div class="pagination">{{ $groups->links() }}</div>@endif
                </div>
            </section>
        </div>
        <aside class="detail-side-stack">
            <section class="panel">
                <h2 class="panel-heading"><x-icon name="info" :size="19" />بيانات الكورس</h2>
                <div class="panel-body">
                    <dl class="detail-list">
                        <div><dt>حالة النشر</dt><dd><x-badge :status="$course->is_published ? 'published' : 'draft'" /></dd></div>
                        <div><dt>الساعات المخططة</dt><dd>{{ $course->planned_hours !== null ? (float)$course->planned_hours.' ساعة' : 'غير محددة' }}</dd></div>
                        <div><dt>الرابط المختصر</dt><dd><bdi>{{ $course->slug }}</bdi></dd></div>
                        <div><dt>عدد المجموعات</dt><dd>{{ $groups->total() }}</dd></div>
                    </dl>
                    <form class="mt-6" action="{{ route('admin.courses.publication', $course) }}" method="post">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_published" value="{{ $course->is_published ? '0' : '1' }}">
                        <button class="button button-secondary" type="submit"><x-icon name="eye" :size="17" />{{ $course->is_published ? 'إلغاء نشر الكورس' : 'نشر الكورس' }}</button>
                    </form>
                </div>
            </section>
            <section class="panel danger-panel">
                <h2 class="panel-heading"><x-icon name="trash" :size="18" />حذف الكورس</h2>
                <div class="panel-body">
                    <p class="field-help mb-4">يسمح بالحذف فقط إذا لم يرتبط الكورس بمجموعات أو بيانات تاريخية. يمكن إلغاء نشره بدلًا من الحذف.</p>
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="post" data-confirm="هل تريد حذف هذا الكورس نهائيًا؟ سيرفض النظام الحذف إن وُجدت بيانات مرتبطة به.">
                        @csrf
                        @method('DELETE')
                        <button class="button button-danger button-small" type="submit"><x-icon name="trash" :size="16" />حذف الكورس</button>
                    </form>
                </div>
            </section>
        </aside>
    </div>
</x-layout>
