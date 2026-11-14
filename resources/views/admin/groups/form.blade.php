@php
    $editing = $trainingGroup->exists;
    $statusLabels = ['draft'=>'مسودة','published'=>'منشورة','in_progress'=>'قيد التنفيذ','completed'=>'مكتملة','cancelled'=>'ملغاة'];
    $opensAt = $trainingGroup->registration_opens_at?->copy()->timezone('Africa/Tripoli')->format('Y-m-d\TH:i');
    $closesAt = $trainingGroup->registration_closes_at?->copy()->timezone('Africa/Tripoli')->format('Y-m-d\TH:i');
@endphp
<x-layout :title="$editing ? 'تعديل المجموعة' : 'إضافة مجموعة تدريبية'">
    <x-breadcrumbs :items="[['label'=>'المجموعات التدريبية','url'=>route('admin.training-groups.index')],['label'=>$editing ? 'تعديل المجموعة' : 'مجموعة جديدة']]" />
    <x-page-heading :title="$editing ? 'تعديل المجموعة التدريبية' : 'إضافة مجموعة تدريبية'" description="حدّد الكورس والمحاضر وبيانات المجموعة. جميع المواعيد بتوقيت طرابلس." />
    @if($courses->isEmpty())
        <div class="notice notice-info"><x-icon name="info" /><p>تحتاج إلى إضافة كورس قبل إنشاء مجموعة. <a class="text-link" href="{{ route('admin.courses.create') }}">إضافة كورس</a></p></div>
    @endif
    @if($instructors->isEmpty())
        <div class="notice notice-info"><x-icon name="info" /><p>لا توجد حسابات محاضرين مؤهلة للإسناد حاليًا.
            @can('manage-accounts')
                <a class="text-link" href="{{ route('admin.instructors.create') }}">إضافة محاضر</a>
            @else
                يرجى التواصل مع المدير لإضافة محاضر نشط.
            @endcan
        </p></div>
    @endif
    <section class="panel admin-form-shell">
        <div class="admin-form-intro"><x-icon name="users" :size="20" /><p>كل مجموعة ترتبط بكورس واحد ومحاضر أساسي واحد، وتُدار السعة وفترة التسجيل الخاصة بها بصورة مستقلة.</p></div>
        <div class="panel-body">
            <form action="{{ $editing ? route('admin.training-groups.update', $trainingGroup) : route('admin.training-groups.store') }}" method="post">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif
                <x-form-section title="التعريف والإسناد" icon="users">
                    <div class="form-grid">
                        <x-select name="course_id" label="الكورس" required>
                            <option value="">اختر الكورس</option>
                            @foreach($courses as $course)<option value="{{ $course->id }}" @selected((string)old('course_id', $trainingGroup->course_id ?? request('course_id')) === (string)$course->id)>{{ $course->title }}{{ $course->is_published ? '' : ' — غير منشور' }}</option>@endforeach
                        </x-select>
                        <x-select name="instructor_user_id" label="المحاضر الأساسي" required help="يظهر المحاضرون المؤهلون للإسناد فقط.">
                            <option value="">اختر المحاضر</option>
                            @foreach($instructors as $instructor)<option value="{{ $instructor->user_id }}" @selected((string)old('instructor_user_id', $trainingGroup->instructor_user_id) === (string)$instructor->user_id)>{{ $instructor->user?->name }}{{ $instructor->professional_title ? ' — '.$instructor->professional_title : '' }}</option>@endforeach
                        </x-select>
                        <x-input name="code" label="رمز المجموعة" :value="$trainingGroup->code" dir="ltr" maxlength="50" required help="رمز فريد يميّز هذه المجموعة عن بقية المجموعات." />
                        <x-input name="capacity" label="السعة الكلية" type="number" :value="$trainingGroup->capacity" min="1" max="65535" step="1" required help="لا يمكن تخفيض السعة عن عدد المقاعد المشغولة والمحجوزة." />
                    </div>
                </x-form-section>
                <x-form-section title="فترة التسجيل والحالة" icon="calendar" description="اختر فترة زمنية صحيحة؛ يجب أن يأتي الإغلاق بعد الفتح.">
                    <div class="form-grid">
                        <x-input name="registration_opens_at" label="فتح التسجيل · توقيت طرابلس" type="datetime-local" :value="$opensAt" required dir="ltr" />
                        <x-input name="registration_closes_at" label="إغلاق التسجيل · توقيت طرابلس" type="datetime-local" :value="$closesAt" required dir="ltr" />
                        <x-select name="status" label="حالة المجموعة" required help="نشر المجموعة يتطلب أن يكون الكورس منشورًا.">
                            @foreach($statusLabels as $value=>$label)<option value="{{ $value }}" @selected(old('status', $trainingGroup->status ?? 'draft') === $value)>{{ $label }}</option>@endforeach
                        </x-select>
                    </div>
                </x-form-section>
                <x-form-section title="رسوم التسجيل الحضوري" icon="tag" description="تُستوفى الرسوم حضوريًا عند التأكيد النهائي. لا يمكن تغيير القيمة أو العملة بعد وجود سجل تحصيل مؤكد، وتبقى محفوظة عند الانسحاب.">
                    <div class="form-grid">
                        <x-input name="fee_amount" label="قيمة الرسوم" type="number" :value="$trainingGroup->fee_amount" min="0" max="99999999.99" step="0.01" dir="ltr" help="اختياري. اتركه فارغًا إذا لم تكن الرسوم محددة." />
                        <x-input name="currency_code" label="رمز العملة" :value="$trainingGroup->currency_code" maxlength="3" minlength="3" dir="ltr" placeholder="مثال: LYD" help="رمز العملة من ثلاثة أحرف، عند تحديد الرسوم." />
                    </div>
                </x-form-section>
                <x-form-actions :cancel="route('admin.training-groups.index')" :label="$editing ? 'حفظ التغييرات' : 'إضافة المجموعة'" />
            </form>
        </div>
    </section>
</x-layout>
