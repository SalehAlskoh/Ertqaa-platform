@php($selectedGroup = old('training_group_id', request('group')))
<x-layout title="توليد جدول المحاضرات">
    <x-breadcrumbs :items="[['label'=>'المحاضرات','url'=>route('admin.lectures.index')],['label'=>'توليد الجدول']]" />
    <x-page-heading title="توليد جدول المحاضرات" description="أنشئ المواعيد الأسبوعية دفعة واحدة لمجموعة لم تُضف لها محاضرات بعد." />
    <div class="notice notice-info"><x-icon name="info" /><p>التوليد متاح مرة واحدة فقط للمجموعة الفارغة. يجب أن يطابق تاريخ البداية أحد أيام الأسبوع المختارة، وأن يكون وقت النهاية بعد البداية في اليوم نفسه.</p></div>
    <section class="panel admin-form-shell"><div class="panel-body">
        <form action="{{ route('admin.lectures.generate.store') }}" method="post">
            @csrf
            <x-form-section title="المجموعة والعدد" icon="book">
                <div class="form-grid">
                    <div class="md:col-span-2"><x-select name="training_group_id" label="المجموعة التدريبية" required>
                        <option value="">اختر مجموعة دون محاضرات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" @selected((string)$selectedGroup === (string)$group->id) @disabled($group->lectures_count > 0)>{{ $group->code }} · {{ $group->course?->title }}{{ $group->lectures_count > 0 ? ' — لديها محاضرات بالفعل' : '' }}</option>
                        @endforeach
                    </x-select></div>
                    <x-input name="first_date" label="تاريخ أول محاضرة" type="date" dir="ltr" required />
                    <x-input name="count" label="عدد المحاضرات" type="number" min="1" max="200" step="1" required help="من محاضرة واحدة إلى 200 محاضرة." />
                </div>
            </x-form-section>
            <x-form-section title="الأيام والوقت · طرابلس" icon="calendar">
                <fieldset class="field">
                    <legend class="field-label">أيام المحاضرات <span class="required-mark" aria-hidden="true">*</span></legend>
                    <div class="weekday-options">
                        @foreach([0=>'الأحد',1=>'الإثنين',2=>'الثلاثاء',3=>'الأربعاء',4=>'الخميس',5=>'الجمعة',6=>'السبت'] as $value=>$label)
                            <label class="weekday-choice"><input type="checkbox" name="weekdays[]" value="{{ $value }}" @checked(in_array((string)$value, array_map('strval', (array)old('weekdays', [])), true))><span>{{ $label }}</span></label>
                        @endforeach
                    </div>
                    <p class="field-help">اختر يومًا واحدًا على الأقل. يكرر النظام الأيام المختارة حتى يكتمل العدد.</p>
                    @error('weekdays')<p class="field-error">{{ $message }}</p>@enderror
                    @error('weekdays.*')<p class="field-error">{{ $message }}</p>@enderror
                </fieldset>
                <div class="form-grid mt-6">
                    <x-input name="start_time" label="وقت البداية" type="time" dir="ltr" required />
                    <x-input name="end_time" label="وقت النهاية" type="time" dir="ltr" required />
                </div>
            </x-form-section>
            <x-form-actions :cancel="route('admin.lectures.index')" label="توليد المحاضرات" />
        </form>
    </div></section>
</x-layout>
