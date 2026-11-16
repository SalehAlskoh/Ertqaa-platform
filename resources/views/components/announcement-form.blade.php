@props(['announcement', 'kind' => 'public', 'courses' => [], 'groups' => []])
@php
    $editing = $announcement->exists;
    $targeted = $kind === 'student';
    $prefix = $targeted ? 'admin.student-announcements.' : 'admin.announcements.';
    $targetType = old('target_type', $announcement->course_id ? 'course' : 'group');
@endphp
<section class="panel admin-form-shell announcement-form-shell">
    <div class="admin-form-intro"><x-icon :name="$targeted ? 'users' : 'bell'" :size="21" /><p>{{ $targeted ? 'إعلان واحد يستهدف كورسًا أو مجموعة. القراءة للطلاب أصحاب التسجيلات النشطة فقط.' : 'إعلان عام للزوار. يُحفظ الإعلان الجديد مسودة إلى أن تنشره.' }}</p></div>
    <div class="panel-body"><form action="{{ $editing ? route($prefix.'update', $announcement) : route($prefix.'store') }}" method="post" @if(!$targeted) enctype="multipart/form-data" @endif>
        @csrf
        @if($editing)
            @method('PUT')
        @endif
        <x-form-section title="محتوى الإعلان" icon="file"><div class="form-grid"><div class="md:col-span-2"><x-input name="title" label="عنوان الإعلان" :value="$announcement->title" maxlength="200" required help="عنوان واضح لا يتجاوز 200 حرف." /></div><div class="md:col-span-2"><x-textarea name="body" label="نص الإعلان" :value="$announcement->body" rows="10" required help="نص عادي دون HTML. يحافظ العرض على فواصل الأسطر، والحد الأقصى 65535 بايت." /></div></div></x-form-section>
        @if($targeted)
            <x-form-section title="الجمهور المستهدف" icon="users">
                <x-select name="target_type" label="نوع الجمهور" required data-announcement-target><option value="course" @selected($targetType === 'course')>طلاب كورس</option><option value="group" @selected($targetType === 'group')>طلاب مجموعة تدريبية</option></x-select>
                <div class="announcement-audience-fields">
                    <div data-announcement-audience="course"><x-select name="course_id" label="الكورس" help="مطلوب عند اختيار كورس؛ يصل الإعلان إلى أصحاب التسجيلات النشطة في مجموعاته."><option value="">اختر الكورس</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected((string)old('course_id', $announcement->course_id) === (string)$course->id)>{{ $course->title }}</option>@endforeach</x-select></div>
                    <div data-announcement-audience="group"><x-select name="training_group_id" label="المجموعة التدريبية" help="مطلوبة عند اختيار مجموعة؛ لا يشمل الإعلان الطلبات المبدئية أو المنسحبين."><option value="">اختر المجموعة</option>@foreach($groups as $group)<option value="{{ $group->id }}" @selected((string)old('training_group_id', $announcement->training_group_id) === (string)$group->id)>{{ $group->code }} — {{ $group->course?->title }}</option>@endforeach</x-select></div>
                </div>
                <div class="announcement-audience-summary"><x-icon name="shield" :size="20" /><p data-announcement-audience-summary aria-live="polite">{{ $targetType === 'course' ? 'الطلاب أصحاب التسجيلات النشطة في أي مجموعة من الكورس المختار، دون تكرار الطالب.' : 'الطلاب أصحاب التسجيلات النشطة في المجموعة المختارة فقط.' }}</p></div>
                <noscript><p class="field-help">اختر نوع الجمهور ثم املأ حقله فقط، واترك الحقل الآخر فارغًا.</p></noscript>
            </x-form-section>
        @else
            <x-form-section title="صورة مرفقة" icon="file"><div class="field"><label class="field-label" for="image">صورة الإعلان <span class="field-optional">اختياري</span></label><input class="input announcement-file-input" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="image-help{{ $errors->has('image') ? ' image-error' : '' }}" @if($errors->has('image')) aria-invalid="true" @endif><p class="field-help" id="image-help">JPEG أو PNG أو WEBP، بحجم لا يتجاوز 2 ميجابايت. في التعديل، ترك الحقل فارغًا يحفظ الصورة الحالية. اختر الملف مجددًا إذا عاد النموذج بخطأ.</p>@error('image')<p class="field-error" id="image-error">{{ $message }}</p>@enderror</div>@if($announcement->image_url)<div class="announcement-existing-image"><img src="{{ $announcement->image_url }}" alt="الصورة الحالية للإعلان"><span>الصورة الحالية</span></div>@endif</x-form-section>
        @endif
        <div class="notice notice-info"><x-icon name="info" /><p>{{ $editing && $announcement->publication_status === 'published' ? 'الإعلان منشور حاليًا؛ حفظ التعديل لا يرسل إشعار نشر جديدًا. حالة النشر لا تتغير من هذا النموذج.' : 'يحفظ هذا النموذج المحتوى فقط. يمكنك نشر الإعلان من صفحة التفاصيل بعد مراجعته.' }}</p></div>
        <x-form-actions :cancel="route($prefix.'index')" :label="$editing ? 'حفظ التغييرات' : 'حفظ المسودة'" />
    </form></div>
</section>
