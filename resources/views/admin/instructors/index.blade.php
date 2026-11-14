<x-layout title="إدارة المحاضرين">
    <x-page-heading title="المحاضرون" description="إدارة الملفات المهنية للمحاضرين ومراجعة المجموعات المسندة إليهم."><a class="button" href="{{ route('admin.instructors.create') }}"><x-icon name="plus" :size="18" />إضافة محاضر</a></x-page-heading>
    <section class="filter-panel" aria-label="البحث عن المحاضرين">
        <form class="filters" action="{{ route('admin.instructors.index') }}" method="get">
            <div class="field search-field"><label class="field-label" for="q">البحث عن محاضر</label><div class="input-with-icon"><x-icon name="search" :size="19" /><input class="input" id="q" name="q" value="{{ request('q') }}" placeholder="الاسم أو البريد الإلكتروني…" maxlength="200"></div></div>
            <div class="filter-actions"><button class="button" type="submit"><x-icon name="search" :size="17" />بحث</button>@if(request()->filled('q'))<a class="button button-secondary" href="{{ route('admin.instructors.index') }}"><x-icon name="refresh" :size="16" />مسح البحث</a>@endif</div>
        </form>
    </section>
    @if($instructors->count())
        <x-data-table title="سجل المحاضرين" :count="$instructors->total()">
            <x-slot:head><tr><th scope="col">المحاضر</th><th scope="col">المسمى المهني</th><th scope="col">المجموعات</th><th scope="col">حالة الحساب</th><th scope="col">الإجراءات</th></tr></x-slot:head>
            @foreach($instructors as $instructor)
                <tr>
                    <td><div class="admin-table-person"><span class="account-avatar" aria-hidden="true">{{ mb_substr($instructor->user?->name ?? 'م', 0, 1) }}</span><div><a class="table-primary" href="{{ route('admin.instructors.show', $instructor) }}">{{ $instructor->user?->name }}</a><div class="table-secondary"><bdi>{{ $instructor->user?->email }}</bdi></div></div></div></td>
                    <td>{{ $instructor->professional_title ?: 'غير محدد' }}</td>
                    <td><span class="linked-count"><x-icon name="users" :size="15" />{{ $instructor->training_groups_count }} مجموعة</span></td>
                    <td><x-badge :status="$instructor->user?->is_active ? 'active' : 'inactive'" /></td>
                    <td><div class="row-actions"><a class="icon-button" href="{{ route('admin.instructors.show', $instructor) }}" aria-label="تفاصيل المحاضر {{ $instructor->user?->name }}" title="التفاصيل"><x-icon name="eye" :size="18" /></a><a class="icon-button" href="{{ route('admin.instructors.edit', $instructor) }}" aria-label="تعديل المحاضر {{ $instructor->user?->name }}" title="تعديل"><x-icon name="edit" :size="17" /></a></div></td>
                </tr>
            @endforeach
            @if($instructors->hasPages())
                <x-slot:pagination>{{ $instructors->withQueryString()->links() }}</x-slot:pagination>
            @endif
        </x-data-table>
    @else
        <section class="panel"><x-empty title="لا يوجد محاضرون مطابقون" description="أضف محاضرًا جديدًا أو ابحث باسم أو بريد إلكتروني مختلف."><a class="button button-secondary" href="{{ route('admin.instructors.create') }}"><x-icon name="plus" :size="17" />إضافة محاضر</a></x-empty></section>
    @endif
</x-layout>
