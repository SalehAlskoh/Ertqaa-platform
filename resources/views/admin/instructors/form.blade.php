@php($editing = $instructor->exists)
@php($existingUser = $existingUser ?? null)
<x-layout :title="$editing ? 'تعديل المحاضر' : 'إضافة محاضر'">
    <x-breadcrumbs :items="[['label'=>'المحاضرون','url'=>route('admin.instructors.index')],['label'=>$editing ? 'تعديل المحاضر' : 'محاضر جديد']]" />
    <x-page-heading :title="$editing ? 'تعديل بيانات المحاضر' : 'إضافة محاضر'" description="تجهيز حساب المحاضر وملفه المهني، مع الحفاظ على بيانات الحسابات الموجودة وأدوارها." />
    <div class="admin-form-shell">
        @unless($editing)
            @include('admin.accounts._lookup', ['lookupRoute'=>route('admin.instructors.create'), 'roleLabel'=>'المحاضر', 'existingUser'=>$existingUser])
        @endunless
        <section class="panel">
            <div class="panel-body">
                <form action="{{ $editing ? route('admin.instructors.update', $instructor) : route('admin.instructors.store') }}" method="post">
                    @csrf
                    @if($editing)
                        @method('PUT')
                    @endif
                    <x-form-section title="بيانات الحساب" icon="user">
                        @if($editing)
                            <div class="form-grid">
                                <x-input name="name" label="الاسم الكامل" :value="$instructor->user?->name" maxlength="150" required />
                                <div class="field"><label class="field-label" for="account-email">البريد الإلكتروني</label><input id="account-email" class="input" type="email" value="{{ $instructor->user?->email }}" dir="ltr" readonly><p class="field-help">البريد معرّف للحساب ولا يُعدّل من الملف المهني.</p></div>
                            </div>
                        @else
                            @include('admin.accounts._create-fields', ['roleLabel'=>'المحاضر', 'existingUser'=>$existingUser])
                        @endif
                    </x-form-section>
                    <x-form-section title="الملف المهني" icon="briefcase" description="قد تظهر المعلومات المهنية للعامة ضمن تفاصيل المجموعات.">
                        <div class="form-grid">
                            <x-input name="professional_title" label="المسمى المهني" :value="$instructor->professional_title" maxlength="150" />
                            <x-input name="experience_years" label="سنوات الخبرة" :value="$instructor->experience_years" type="number" min="0" max="65535" step="1" />
                            <div class="md:col-span-2"><x-textarea name="biography" label="نبذة مهنية" :value="$instructor->biography" rows="6" help="اشرح خبرة المحاضر وتخصصه دون إضافة بيانات تواصل شخصية." /></div>
                        </div>
                    </x-form-section>
                    <x-form-actions :cancel="route('admin.instructors.index')" :label="$editing ? 'حفظ التغييرات' : 'إضافة المحاضر'" />
                </form>
            </div>
        </section>
    </div>
</x-layout>
