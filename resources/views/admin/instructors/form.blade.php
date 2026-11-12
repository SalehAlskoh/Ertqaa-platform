@php($editing = $instructor->exists)
@php($existingUser = $existingUser ?? null)
<x-layout :title="$editing ? 'تعديل المحاضر' : 'إضافة محاضر'">
    <nav class="breadcrumb" aria-label="مسار الصفحة"><a href="{{ route('admin.instructors.index') }}">المحاضرون</a><span aria-hidden="true">/</span><span>{{ $editing ? 'تعديل المحاضر' : 'محاضر جديد' }}</span></nav>
    <x-page-heading :title="$editing ? 'تعديل بيانات المحاضر' : 'إضافة محاضر'" eyebrow="إدارة المحاضرين" description="اربط الحساب بالملف المهني دون تكرار بيانات الهوية أو استبدال أدواره الأخرى." />
    <div class="max-w-5xl">
        @unless($editing)
            @include('admin.accounts._lookup', ['lookupRoute'=>route('admin.instructors.create'), 'roleLabel'=>'المحاضر', 'existingUser'=>$existingUser])
        @endunless
    <section class="panel"><div class="panel-body"><form action="{{ $editing ? route('admin.instructors.update', $instructor) : route('admin.instructors.store') }}" method="post">@csrf @if($editing) @method('PUT') @endif
        <section class="form-section">
            <h2 class="section-title">بيانات الحساب</h2>
            @if($editing)
                <div class="form-grid"><x-input name="name" label="الاسم الكامل" :value="$instructor->user?->name" maxlength="150" required /><div class="field"><label class="field-label" for="account-email">البريد الإلكتروني</label><input id="account-email" class="input" type="email" value="{{ $instructor->user?->email }}" dir="ltr" readonly><p class="field-help">البريد معرّف للحساب ولا يُعدّل من الملف المهني.</p></div></div>
            @else
                @include('admin.accounts._create-fields', ['roleLabel'=>'المحاضر', 'existingUser'=>$existingUser])
            @endif
        </section>
        <section class="form-section"><h2 class="section-title">الملف المهني</h2><div class="form-grid"><x-input name="professional_title" label="المسمى المهني" :value="$instructor->professional_title" maxlength="150" /><x-input name="experience_years" label="سنوات الخبرة" :value="$instructor->experience_years" type="number" min="0" max="65535" step="1" /><div class="md:col-span-2"><x-textarea name="biography" label="نبذة مهنية" :value="$instructor->biography" rows="6" help="قد تظهر هذه المعلومات للزوار ضمن تفاصيل المجموعة. لا تضف بيانات تواصل شخصية." /></div></div></section>
        <div class="form-actions"><x-submit :label="$editing ? 'حفظ التغييرات' : 'إضافة المحاضر'" /><a class="button button-secondary" href="{{ route('admin.instructors.index') }}">إلغاء</a></div>
    </form></div></section></div>
</x-layout>
