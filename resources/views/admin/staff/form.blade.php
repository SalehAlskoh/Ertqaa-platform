@php($editing = $staffMember->exists)
@php($existingUser = $existingUser ?? null)
<x-layout :title="$editing ? 'تعديل الموظف' : 'إضافة موظف'">
    <x-breadcrumbs :items="[['label'=>'الموظفون','url'=>route('admin.staff.index')],['label'=>$editing ? 'تعديل الموظف' : 'موظف جديد']]" />
    <x-page-heading :title="$editing ? 'تعديل بيانات الموظف' : 'إضافة موظف'" description="يتيح دور الموظف إدارة الكورسات والتصنيفات والمجموعات، دون إدارة الحسابات أو الأدوار." />
    <div class="admin-form-shell">
        @unless($editing)
            @include('admin.accounts._lookup', ['lookupRoute'=>route('admin.staff.create'), 'roleLabel'=>'الموظف', 'existingUser'=>$existingUser])
        @endunless
        <section class="panel">
            <div class="panel-body">
                <form action="{{ $editing ? route('admin.staff.update', $staffMember) : route('admin.staff.store') }}" method="post">
                    @csrf
                    @if($editing)
                        @method('PUT')
                    @endif
                    <x-form-section title="بيانات حساب الموظف" icon="briefcase">
                        @if($editing)
                            <div class="form-grid">
                                <x-input name="name" label="الاسم الكامل" :value="$staffMember->name" maxlength="150" required />
                                <div class="field"><label class="field-label" for="account-email">البريد الإلكتروني</label><input class="input" id="account-email" value="{{ $staffMember->email }}" type="email" dir="ltr" readonly><p class="field-help">البريد معرّف للحساب ولا يُعدّل من هذه الشاشة.</p></div>
                            </div>
                            <p class="field-help mt-5">لتفعيل الحساب أو تعطيله، استخدم قائمة الموظفين. التعطيل يؤثر على جميع أدوار الحساب.</p>
                        @else
                            @include('admin.accounts._create-fields', ['roleLabel'=>'الموظف', 'existingUser'=>$existingUser])
                        @endif
                    </x-form-section>
                    <x-form-actions :cancel="route('admin.staff.index')" :label="$editing ? 'حفظ التغييرات' : 'إضافة الموظف'" />
                </form>
            </div>
        </section>
    </div>
</x-layout>
