<x-layout title="إدارة إعلانات المركز">
    <x-breadcrumbs :items="[['label'=>'إدارة إعلانات المركز']]" />
    <x-page-heading title="إعلانات المركز" description="أنشئ أخبار المركز، راجع محتواها، ثم انشرها للزوار دون حذف سجل الإعلان."><a class="button" href="{{ route('admin.announcements.create') }}"><x-icon name="plus" :size="18" />إضافة إعلان عام</a></x-page-heading>
    <div class="notice notice-info"><x-icon name="info" /><p>الإعلان العام متاح للجميع بعد النشر. للإعلانات الخاصة بالمسجلين استخدم <a class="text-link" href="{{ route('admin.student-announcements.index') }}">إعلانات الطلاب</a>.</p></div>
    <section class="filter-panel" aria-label="البحث وتصفية الإعلانات العامة"><form class="filters" action="{{ route('admin.announcements.index') }}" method="get"><div class="field search-field"><label class="field-label" for="q">البحث</label><div class="input-with-icon"><x-icon name="search" :size="18" /><input class="input" name="q" id="q" value="{{ request('q') }}" maxlength="200" placeholder="العنوان أو المحتوى…"></div></div><div class="field"><label class="field-label" for="status">حالة النشر</label><select class="input" name="status" id="status"><option value="">جميع الحالات</option>@foreach(['draft'=>'مسودة','published'=>'منشور','scheduled'=>'نشر مستقبلي'] as $value=>$label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div><div class="filter-actions"><button class="button" type="submit"><x-icon name="filter" :size="17" />تصفية</button>@if(request()->hasAny(['q','status']))<a class="button button-secondary" href="{{ route('admin.announcements.index') }}">مسح التصفية</a>@endif</div></form></section>
    @if($announcements->count())
        <x-data-table title="سجل الإعلانات العامة" :count="$announcements->total()" label="الإعلانات العامة المطابقة للتصفية وحالات نشرها وإجراءاتها" class="announcement-management-table">
            <x-slot:head><tr><th scope="col">العنوان والمحتوى</th><th scope="col">المسؤول</th><th scope="col">الحالة</th><th scope="col">النشر · طرابلس</th><th scope="col">الإجراءات</th></tr></x-slot:head>
            @foreach($announcements as $announcement)<tr><td class="announcement-title-cell"><a class="table-primary" href="{{ route('admin.announcements.show', $announcement) }}">{{ $announcement->title }}</a><p class="table-secondary">{{ \Illuminate\Support\Str::limit($announcement->body, 90) }}</p></td><td>{{ $announcement->author?->name }}</td><td><x-announcement-status :announcement="$announcement" /></td><td><x-date :value="$announcement->published_at" /></td><td><x-announcement-actions :announcement="$announcement" /></td></tr>@endforeach
            <x-slot:pagination>
                {{ $announcements->links() }}
            </x-slot:pagination>
        </x-data-table>
    @else
        <section class="panel"><x-empty title="لا توجد إعلانات مطابقة" description="ابدأ بإعلان عام جديد أو غيّر خيارات البحث والتصفية."><a class="button button-secondary" href="{{ route('admin.announcements.create') }}">إضافة إعلان عام</a></x-empty></section>
    @endif
</x-layout>
