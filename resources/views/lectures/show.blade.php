@php
    $group = $lecture->trainingGroup;
    $listRoute = auth()->user()->can('manage-catalog') ? 'admin.lectures.index' : (auth()->user()->can('teach') ? 'instructor.lectures.index' : 'student.lectures.index');
@endphp
<x-layout title="تفاصيل المحاضرة">
    <x-breadcrumbs :items="[['label'=>'المحاضرات','url'=>route($listRoute)],['label'=>'المحاضرة '.$lecture->lecture_number]]" />
    <x-page-heading :title="$lecture->title ?: 'محاضرة رقم '.$lecture->lecture_number" description="موعد المحاضرة وحالتها الحالية. جميع الأوقات بتوقيت طرابلس.">
        <x-badge :status="$lecture->status" />
        @if($lecture->status === 'scheduled')
            @can('update', $lecture)
                <a class="button" href="{{ route('admin.lectures.edit', $lecture) }}"><x-icon name="edit" :size="17" />تعديل المحاضرة</a>
            @endcan
        @endif
    </x-page-heading>
    <div class="workflow-grid">
        <div class="workflow-main">
            <section class="panel"><h2 class="panel-heading"><x-icon name="calendar" :size="19" />بيانات المحاضرة</h2><div class="panel-body">
                <dl class="detail-list">
                    <div><dt>رقم المحاضرة في المجموعة</dt><dd>{{ $lecture->lecture_number }}</dd></div>
                    <div><dt>الكورس</dt><dd>{{ $group?->course?->title }}</dd></div>
                    <div><dt>المجموعة</dt><dd><bdi>{{ $group?->code }}</bdi></dd></div>
                    <div><dt>المحاضر</dt><dd>{{ $group?->instructor?->user?->name ?? 'غير محدد' }}</dd></div>
                    <div><dt>بداية المحاضرة</dt><dd><x-date :value="$lecture->starts_at" /></dd></div>
                    <div><dt>نهاية المحاضرة</dt><dd><x-date :value="$lecture->ends_at" /></dd></div>
                </dl>
                @if($lecture->change_reason)
                    <div class="workflow-reason mt-6"><h3 class="field-label">سبب آخر تغيير</h3><p class="detail-prose">{{ $lecture->change_reason }}</p></div>
                @endif
            </div></section>
        </div>
        @if($lecture->status === 'scheduled')
            @can('update', $lecture)
                <aside class="workflow-side">
                    <section class="panel"><h2 class="panel-heading">إدارة الحالة</h2><div class="panel-body form-stack">
                        <p class="field-help">الإكمال والإلغاء إجراءان نهائيان لهذه المحاضرة. لا تُحذف بياناتها من الجدول.</p>
                        <form action="{{ route('admin.lectures.complete', $lecture) }}" method="post" data-confirm="هل تريد تسجيل هذه المحاضرة كمكتملة؟">
                            @csrf
                            @method('PATCH')
                            <button class="button" type="submit"><x-icon name="check-circle" :size="17" />تسجيل اكتمال المحاضرة</button>
                        </form>
                        <form class="form-stack workflow-reject" action="{{ route('admin.lectures.cancel', $lecture) }}" method="post" data-confirm="هل تريد إلغاء هذه المحاضرة؟ سيُحفظ سبب الإلغاء دون حذف المحاضرة.">
                            @csrf
                            @method('PATCH')
                            <x-textarea name="change_reason" label="سبب الإلغاء" maxlength="65000" rows="3" required />
                            <button class="button button-danger" type="submit">إلغاء المحاضرة</button>
                        </form>
                    </div></section>
                </aside>
            @endcan
        @endif
    </div>
</x-layout>
