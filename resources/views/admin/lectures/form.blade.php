@php
    $editing = $lecture->exists;
    $selectedGroup = old('training_group_id', request('group', $lecture->training_group_id));
@endphp
<x-layout :title="$editing ? 'تعديل المحاضرة' : 'إضافة محاضرة'">
    <x-breadcrumbs :items="[['label'=>'المحاضرات','url'=>route('admin.lectures.index')],['label'=>$editing ? 'تعديل المحاضرة' : 'محاضرة جديدة']]" />
    <x-page-heading :title="$editing ? 'تعديل المحاضرة' : 'إضافة محاضرة'" description="أدخل المواعيد بتوقيت طرابلس. رقم المحاضرة يحدده النظام داخل المجموعة." />
    <section class="panel admin-form-shell"><div class="admin-form-intro"><x-icon name="calendar" :size="20" /><p>{{ $editing ? 'يُحفظ سبب التعديل مع تغيير العنوان أو الموعد.' : 'تُضاف المحاضرة إلى نهاية ترقيم المجموعة بحالة مجدولة.' }}</p></div><div class="panel-body">
        <form action="{{ $editing ? route('admin.lectures.update', $lecture) : route('admin.lectures.store') }}" method="post">
            @csrf
            @if($editing)
                @method('PUT')
            @endif
            <x-form-section title="المحاضرة والمجموعة" icon="book">
                <div class="form-grid">
                    @if($editing)
                        <div class="record-readonly"><span>المجموعة</span><strong>{{ $lecture->trainingGroup?->course?->title }} · <bdi>{{ $lecture->trainingGroup?->code }}</bdi></strong></div>
                        <div class="record-readonly"><span>رقم المحاضرة</span><strong>{{ $lecture->lecture_number }}</strong></div>
                    @else
                        <div class="md:col-span-2"><x-select name="training_group_id" label="المجموعة التدريبية" required>
                            <option value="">اختر المجموعة</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" @selected((string)$selectedGroup === (string)$group->id)>{{ $group->code }} · {{ $group->course?->title }}</option>
                            @endforeach
                        </x-select></div>
                    @endif
                    <div class="md:col-span-2"><x-input name="title" label="عنوان المحاضرة" :value="$lecture->title" maxlength="200" help="اختياري؛ يمكن تمييز المحاضرة برقمها عند ترك العنوان فارغًا." /></div>
                    <x-input name="starts_at" label="البداية · توقيت طرابلس" type="datetime-local" :value="$lecture->starts_at?->copy()->timezone('Africa/Tripoli')->format('Y-m-d\TH:i')" dir="ltr" required />
                    <x-input name="ends_at" label="النهاية · توقيت طرابلس" type="datetime-local" :value="$lecture->ends_at?->copy()->timezone('Africa/Tripoli')->format('Y-m-d\TH:i')" dir="ltr" help="يجب أن تأتي النهاية بعد البداية." required />
                </div>
            </x-form-section>
            @if($editing)
                <x-form-section title="سبب التعديل" icon="file"><x-textarea name="change_reason" label="وضح سبب التغيير" rows="3" maxlength="65000" required /></x-form-section>
            @endif
            <x-form-actions :cancel="$editing ? route('lectures.show', $lecture) : route('admin.lectures.index')" :label="$editing ? 'حفظ التعديل' : 'إضافة المحاضرة'" />
        </form>
    </div></section>
</x-layout>
