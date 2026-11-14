@php
    $indexRoute = $area.'.lectures.index';
@endphp
<x-layout title="المحاضرات">
    <x-page-heading title="{{ $area === 'admin' ? 'إدارة المحاضرات' : 'محاضراتي' }}" description="الجدول الفعلي للمحاضرات ومواعيدها بتوقيت طرابلس.">
        @can('manage-catalog')
            <a class="button button-secondary" href="{{ route('admin.lectures.generate') }}"><x-icon name="calendar" :size="17" />توليد جدول محاضرات</a>
            <a class="button" href="{{ route('admin.lectures.create') }}"><x-icon name="plus" :size="17" />إضافة محاضرة</a>
        @endcan
    </x-page-heading>
    @can('manage-catalog')
        <div class="notice notice-info"><x-icon name="info" /><p>يمكن توليد الجدول مرة واحدة للمجموعة التي لا تحتوي على محاضرات. تُضاف المحاضرات اللاحقة منفردة، ويُسجّل سبب أي تعديل أو إلغاء.</p></div>
    @endcan
    <section class="filter-panel" aria-label="تصفية المحاضرات">
        <form class="filters" action="{{ route($indexRoute) }}" method="get">
            <div class="field search-field"><label class="field-label" for="q">البحث عن محاضرة</label><div class="input-with-icon"><x-icon name="search" :size="18" /><input class="input" id="q" name="q" maxlength="200" value="{{ request('q') }}" placeholder="عنوان المحاضرة أو رمز المجموعة…"></div></div>
            <x-select name="group" label="المجموعة">
                <option value="">جميع المجموعات المتاحة لك</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected((string)request('group') === (string)$group->id)>{{ $group->code }} · {{ $group->course?->title }}</option>
                @endforeach
            </x-select>
            <x-select name="status" label="الحالة">
                <option value="">جميع الحالات</option>
                @foreach(['scheduled'=>'مجدولة','completed'=>'مكتملة','cancelled'=>'ملغاة'] as $value=>$label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </x-select>
            <div class="filter-actions"><button class="button" type="submit">تطبيق</button>
                @if(request()->hasAny(['q','group','status']))
                    <a class="button button-secondary" href="{{ route($indexRoute) }}">مسح التصفية</a>
                @endif
            </div>
        </form>
    </section>
    @if($lectures->count())
        <x-data-table class="lecture-management-table" title="جدول المحاضرات" :count="$lectures->total()">
            <x-slot:head><tr><th scope="col">المحاضرة</th><th scope="col">الكورس والمجموعة</th><th scope="col">الموعد · طرابلس</th><th scope="col">الحالة</th><th scope="col">الإجراءات</th></tr></x-slot:head>
            @foreach($lectures as $lecture)
                <tr>
                    <td><strong class="table-primary">{{ $lecture->title ?: 'محاضرة رقم '.$lecture->lecture_number }}</strong><span class="table-secondary">المحاضرة {{ $lecture->lecture_number }}</span></td>
                    <td><strong class="table-primary">{{ $lecture->trainingGroup?->course?->title }}</strong><span class="table-secondary"><bdi>{{ $lecture->trainingGroup?->code }}</bdi> · {{ $lecture->trainingGroup?->instructor?->user?->name ?? 'المحاضر غير محدد' }}</span></td>
                    <td><div class="lecture-time-cell"><span>من <x-date :value="$lecture->starts_at" /></span><span>إلى <x-date :value="$lecture->ends_at" /></span></div></td>
                    <td><x-badge :status="$lecture->status" /></td>
                    <td><div class="row-actions"><a class="button button-secondary button-small" href="{{ route('lectures.show', $lecture) }}">التفاصيل</a>
                        @if($lecture->status === 'scheduled')
                            @can('update', $lecture)
                                <a class="button button-secondary button-small" href="{{ route('admin.lectures.edit', $lecture) }}">تعديل</a>
                            @endcan
                        @endif
                    </div></td>
                </tr>
            @endforeach
            @if($lectures->hasPages())
                <x-slot:pagination>{{ $lectures->withQueryString()->links() }}</x-slot:pagination>
            @endif
        </x-data-table>
    @else
        <section class="panel"><x-empty title="لا توجد محاضرات مطابقة" description="ستظهر هنا المحاضرات المتاحة لك. جرّب تغيير التصفية إن كنت تبحث عن موعد محدد." /></section>
    @endif
</x-layout>
