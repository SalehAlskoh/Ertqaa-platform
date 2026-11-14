@php($statusLabels = ['draft'=>'مسودة','published'=>'منشورة','in_progress'=>'قيد التنفيذ','completed'=>'مكتملة','cancelled'=>'ملغاة'])
<x-layout title="إدارة المجموعات التدريبية">
    <x-page-heading title="المجموعات التدريبية" description="إدارة طرح الكورسات وإسناد المحاضرين والسعة وفترات التسجيل."><a class="button" href="{{ route('admin.training-groups.create') }}"><x-icon name="plus" :size="18" />إضافة مجموعة</a></x-page-heading>
    <section class="filter-panel" aria-label="تصفية المجموعات">
        <form class="filters" action="{{ route('admin.training-groups.index') }}" method="get">
            <div class="field search-field"><label class="field-label" for="q">البحث برمز المجموعة</label><div class="input-with-icon"><x-icon name="search" :size="19" /><input class="input" id="q" name="q" value="{{ request('q') }}" maxlength="200" placeholder="أدخل رمز المجموعة…"></div></div>
            <div class="field"><label class="field-label" for="course">الكورس</label><select class="input" id="course" name="course"><option value="">جميع الكورسات</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected((string)request('course') === (string)$course->id)>{{ $course->title }}</option>@endforeach</select></div>
            <div class="field"><label class="field-label" for="status">الحالة</label><select class="input" id="status" name="status"><option value="">جميع الحالات</option>@foreach($statusLabels as $value=>$label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="filter-actions"><button class="button" type="submit"><x-icon name="filter" :size="17" />تصفية</button>@if(request()->hasAny(['q','course','status']))<a class="icon-button icon-button-bordered" href="{{ route('admin.training-groups.index') }}" aria-label="مسح التصفية" title="مسح التصفية"><x-icon name="refresh" :size="18" /></a>@endif</div>
        </form>
    </section>
    @if($groups->count())
        <x-data-table title="سجل المجموعات" :count="$groups->total()">
            <x-slot:head><tr><th scope="col">المجموعة والكورس</th><th scope="col">المحاضر</th><th scope="col">السعة</th><th scope="col">الرسوم المعروضة</th><th scope="col">الحالة</th><th scope="col">الإجراءات</th></tr></x-slot:head>
            @foreach($groups as $group)
                <tr>
                    <td><a class="table-primary" href="{{ route('admin.training-groups.show', $group) }}"><bdi>{{ $group->code }}</bdi></a><div class="table-secondary">{{ $group->course?->title }}</div></td>
                    <td>{{ $group->instructor?->user?->name ?? 'غير محدد' }}</td>
                    <td><span class="table-meta"><x-icon name="seat" :size="16" />{{ $group->capacity }}</span></td>
                    <td>@if($group->fee_amount !== null){{ number_format((float)$group->fee_amount, 2) }} {{ $group->currency_code }}@else<span class="field-help">غير محددة</span>@endif</td>
                    <td><x-badge :status="$group->status" /></td>
                    <td><div class="row-actions"><a class="icon-button" href="{{ route('admin.training-groups.show', $group) }}" aria-label="تفاصيل المجموعة {{ $group->code }}" title="التفاصيل"><x-icon name="eye" :size="18" /></a><a class="icon-button" href="{{ route('admin.training-groups.edit', $group) }}" aria-label="تعديل المجموعة {{ $group->code }}" title="تعديل"><x-icon name="edit" :size="17" /></a></div></td>
                </tr>
            @endforeach
            @if($groups->hasPages())
                <x-slot:pagination>{{ $groups->withQueryString()->links() }}</x-slot:pagination>
            @endif
        </x-data-table>
    @else
        <section class="panel"><x-empty title="لا توجد مجموعات مطابقة" description="أنشئ مجموعة جديدة أو غيّر خيارات البحث والتصفية."><a class="button button-secondary" href="{{ route('admin.training-groups.create') }}"><x-icon name="plus" :size="17" />إضافة مجموعة</a></x-empty></section>
    @endif
</x-layout>
