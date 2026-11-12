@php($editing = $staffMember->exists)
@php($existingUser = $existingUser ?? null)
<x-layout :title="$editing ? 'تعديل الموظف' : 'إضافة موظف'">
    <nav class="breadcrumb" aria-label="مسار الصفحة"><a href="{{ route('admin.staff.index') }}">الموظفون</a><span aria-hidden="true">/</span><span>{{ $editing ? 'تعديل الموظف' : 'موظف جديد' }}</span></nav>
    <x-page-heading :title="$editing ? 'تعديل بيانات الموظف' : 'إضافة موظف'" eyebrow="إدارة الحسابات" description="يتيح دور الموظف إدارة الكورسات والتصنيفات والمجموعات، دون إدارة الحسابات أو الأدوار." />
    <div class="max-w-4xl">
        @unless($editing)
            @include('admin.accounts._lookup', ['lookupRoute'=>route('admin.staff.create'), 'roleLabel'=>'الموظف', 'existingUser'=>$existingUser])
        @endunless
    <section class="panel"><div class="panel-body"><form action="{{ $editing ? route('admin.staff.update', $staffMember) : route('admin.staff.store') }}" method="post">@csrf @if($editing) @method('PUT') @endif
        @if($editing)
            <div class="form-grid"><x-input name="name" label="الاسم الكامل" :value="$staffMember->name" maxlength="150" required /><div class="field"><label class="field-label" for="account-email">البريد الإلكتروني</label><input class="input" id="account-email" value="{{ $staffMember->email }}" type="email" dir="ltr" readonly><p class="field-help">البريد معرّف للحساب ولا يُعدّل من هذه الشاشة.</p></div></div><p class="field-help mt-5">لتفعيل الحساب أو تعطيله، استخدم قائمة الموظفين. التعطيل يؤثر على جميع أدوار الحساب.</p>
        @else
            @include('admin.accounts._create-fields', ['roleLabel'=>'الموظف', 'existingUser'=>$existingUser])
        @endif
        <div class="form-actions"><x-submit :label="$editing ? 'حفظ التغييرات' : 'إضافة الموظف'" /><a class="button button-secondary" href="{{ route('admin.staff.index') }}">إلغاء</a></div>
    </form></div></section></div>
</x-layout>
