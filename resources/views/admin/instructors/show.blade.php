<x-layout :title="$instructor->user?->name ?? 'بيانات المحاضر'">
    <x-breadcrumbs :items="[['label'=>'المحاضرون','url'=>route('admin.instructors.index')],['label'=>$instructor->user?->name ?? 'بيانات المحاضر']]" />
    <section class="panel group-hero">
        <div class="group-hero-header">
            <div class="instructor-hero-summary"><span class="account-avatar" aria-hidden="true">{{ mb_substr($instructor->user?->name ?? 'م', 0, 1) }}</span><div><h1>{{ $instructor->user?->name }}</h1><p>{{ $instructor->professional_title ?: 'الملف المهني للمحاضر' }}</p><div class="professional-meta"><x-badge :status="$instructor->user?->is_active ? 'active' : 'inactive'" /><span class="table-meta"><x-icon name="users" :size="15" />{{ $groups->total() }} مجموعة مسندة</span></div></div></div>
            <a class="button" href="{{ route('admin.instructors.edit', $instructor) }}"><x-icon name="edit" :size="17" />تعديل البيانات</a>
        </div>
    </section>
    <div class="detail-layout">
        <div class="admin-course-body">
            <section class="panel">
                <h2 class="panel-heading"><x-icon name="briefcase" :size="19" />عن المحاضر</h2>
                <div class="panel-body"><p class="detail-prose">{{ $instructor->biography ?: 'لم تتم إضافة نبذة مهنية بعد.' }}</p><dl class="detail-list mt-6"><div><dt>المسمى المهني</dt><dd>{{ $instructor->professional_title ?: 'غير محدد' }}</dd></div><div><dt>سنوات الخبرة</dt><dd>{{ $instructor->experience_years ?? 'غير محددة' }}</dd></div></dl></div>
            </section>
            <section>
                <div class="admin-section-heading"><h2>المجموعات المسندة <span class="result-count">{{ $groups->total() }}</span></h2></div>
                <div class="catalog-list">
                    @forelse($groups as $group)
                        <x-group-row :group="$group" route-name="admin.training-groups.show" />
                    @empty
                        <x-empty title="لا توجد مجموعات مسندة" description="يمكن إسناد المحاضر من شاشة إنشاء المجموعة أو تعديلها." />
                    @endforelse
                    @if($groups->hasPages())<div class="pagination">{{ $groups->links() }}</div>@endif
                </div>
            </section>
        </div>
        <aside class="detail-side-stack">
            <section class="panel">
                <h2 class="panel-heading"><x-icon name="user" :size="19" />الحساب</h2>
                <div class="panel-body"><dl class="detail-list"><div><dt>البريد الإلكتروني</dt><dd><bdi>{{ $instructor->user?->email }}</bdi></dd></div><div><dt>الحالة</dt><dd><x-badge :status="$instructor->user?->is_active ? 'active' : 'inactive'" /></dd></div></dl></div>
            </section>
            <section class="panel danger-panel">
                <h2 class="panel-heading"><x-icon name="shield" :size="18" />حالة الدخول</h2>
                <div class="panel-body">
                    <p class="field-help mb-4">تعطيل الحساب يمنع الدخول بجميع أدواره. تبقى المجموعات والبيانات المرتبطة محفوظة.</p>
                    <form action="{{ route('admin.instructors.status', $instructor) }}" method="post" data-confirm="هل تريد {{ $instructor->user?->is_active ? 'تعطيل' : 'تفعيل' }} هذا الحساب بجميع أدواره؟">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="{{ $instructor->user?->is_active ? '0' : '1' }}">
                        <button class="button {{ $instructor->user?->is_active ? 'button-danger' : 'button-secondary' }}" type="submit">{{ $instructor->user?->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}</button>
                    </form>
                </div>
            </section>
        </aside>
    </div>
</x-layout>
