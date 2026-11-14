@php($editing = $course->exists)
<x-layout :title="$editing ? 'تعديل الكورس' : 'إضافة كورس'">
    <x-breadcrumbs :items="[['label'=>'الكورسات','url'=>route('admin.courses.index')],['label'=>$editing ? 'تعديل الكورس' : 'كورس جديد']]" />
    <x-page-heading :title="$editing ? 'تعديل الكورس' : 'إضافة كورس جديد'" description="عرّف محتوى الكورس وساعاته المخططة، ثم اختر نشره عند اكتمال بياناته." />
    @if($categories->isEmpty())
        <div class="notice notice-info"><x-icon name="info" /><p>تحتاج إلى إضافة تصنيف قبل إنشاء كورس. <a class="text-link" href="{{ route('admin.course-categories.create') }}">إضافة تصنيف الآن</a></p></div>
    @endif
    <section class="panel admin-form-shell">
        <div class="admin-form-intro"><x-icon name="book" :size="20" /><p>الكورس يعرّف المحتوى التدريبي. يُحدد المحاضر والسعة وفترة التسجيل لكل مجموعة مرتبطة به.</p></div>
        <div class="panel-body">
            <form action="{{ $editing ? route('admin.courses.update', $course) : route('admin.courses.store') }}" method="post">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif
                <x-form-section title="المعلومات الأساسية" icon="book">
                    <div class="form-grid">
                        <div class="md:col-span-2"><x-input name="title" label="عنوان الكورس" :value="$course->title" maxlength="200" required /></div>
                        <x-select name="category_id" label="التصنيف" required>
                            <option value="">اختر التصنيف</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string)old('category_id', $course->category_id) === (string)$category->id)>{{ $category->name }}</option>
                            @endforeach
                        </x-select>
                        <x-input name="planned_hours" label="الساعات المخططة" type="number" :value="$course->planned_hours" min="0.01" max="9999.99" step="0.01" help="اختياري؛ عدد موجب لا يتجاوز 9999.99 ساعة." />
                        <div class="md:col-span-2"><x-input name="slug" label="الرابط المختصر" :value="$course->slug" dir="ltr" maxlength="220" help="اختياري؛ يُنشأ تلقائيًا عند تركه فارغًا. يجب أن يكون فريدًا." /></div>
                    </div>
                </x-form-section>
                <x-form-section title="محتوى الكورس" icon="file">
                    <x-textarea name="description" label="وصف الكورس" :value="$course->description" rows="7" help="اشرح موضوع الكورس ومحتواه بوضوح. النص يُعرض كما هو دون تنسيق HTML." />
                </x-form-section>
                <x-form-section title="حالة النشر" icon="eye">
                    <input type="hidden" name="is_published" value="0">
                    <label class="publication-control">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $course->is_published))>
                        <span><strong>نشر الكورس في الدليل العام</strong><p>يمكن للزوار والطلاب الاطلاع على الكورس المنشور. إلغاء النشر يحفظ بياناته ويخفيه عن العامة.</p></span>
                    </label>
                    @error('is_published')<p class="field-error mt-2">{{ $message }}</p>@enderror
                </x-form-section>
                <x-form-actions :cancel="route('admin.courses.index')" :label="$editing ? 'حفظ التغييرات' : 'إضافة الكورس'" />
            </form>
        </div>
    </section>
</x-layout>
