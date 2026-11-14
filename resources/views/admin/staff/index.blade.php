<x-layout title="إدارة الموظفين">
    <x-page-heading title="حسابات الموظفين" description="إدارة الموظفين المخوّلين بإدارة التصنيفات والكورسات والمجموعات التدريبية."><a class="button" href="{{ route('admin.staff.create') }}"><x-icon name="plus" :size="18" />إضافة موظف</a></x-page-heading>
    <section class="filter-panel" aria-label="البحث عن الموظفين">
        <form class="filters" action="{{ route('admin.staff.index') }}" method="get">
            <div class="field search-field"><label class="field-label" for="q">البحث عن موظف</label><div class="input-with-icon"><x-icon name="search" :size="19" /><input class="input" id="q" name="q" value="{{ request('q') }}" maxlength="200" placeholder="الاسم أو البريد الإلكتروني…"></div></div>
            <div class="filter-actions"><button class="button" type="submit"><x-icon name="search" :size="17" />بحث</button>@if(request()->filled('q'))<a class="button button-secondary" href="{{ route('admin.staff.index') }}"><x-icon name="refresh" :size="16" />مسح البحث</a>@endif</div>
        </form>
    </section>
    @if($staff->count())
        <x-data-table title="سجل الموظفين" :count="$staff->total()">
            <x-slot:head><tr><th scope="col">الموظف</th><th scope="col">البريد الإلكتروني</th><th scope="col">حالة الحساب</th><th scope="col">الإجراءات</th></tr></x-slot:head>
            @foreach($staff as $staffMember)
                <tr>
                    <td><div class="admin-table-person"><span class="account-avatar" aria-hidden="true">{{ mb_substr($staffMember->name, 0, 1) }}</span><strong class="table-primary">{{ $staffMember->name }}</strong></div></td>
                    <td><bdi>{{ $staffMember->email }}</bdi></td>
                    <td><x-badge :status="$staffMember->is_active ? 'active' : 'inactive'" /></td>
                    <td><div class="row-actions"><a class="button button-secondary button-small" href="{{ route('admin.staff.edit', $staffMember) }}"><x-icon name="edit" :size="15" />تعديل</a><form action="{{ route('admin.staff.status', $staffMember) }}" method="post" data-confirm="هل تريد {{ $staffMember->is_active ? 'تعطيل' : 'تفعيل' }} حساب الموظف؟ يؤثر ذلك على جميع أدوار الحساب.">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="{{ $staffMember->is_active ? '0' : '1' }}">
                        <button class="button {{ $staffMember->is_active ? 'button-danger' : 'button-secondary' }} button-small" type="submit">{{ $staffMember->is_active ? 'تعطيل' : 'تفعيل' }}</button>
                    </form></div></td>
                </tr>
            @endforeach
            @if($staff->hasPages())
                <x-slot:pagination>{{ $staff->withQueryString()->links() }}</x-slot:pagination>
            @endif
        </x-data-table>
    @else
        <section class="panel"><x-empty title="لا توجد حسابات موظفين مطابقة" description="يمكنك إضافة موظف جديد أو إسناد الدور إلى حساب موجود بعد التحقق من هويته."><a class="button button-secondary" href="{{ route('admin.staff.create') }}"><x-icon name="plus" :size="17" />إضافة موظف</a></x-empty></section>
    @endif
</x-layout>
