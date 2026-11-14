@php($editing = $category->exists)
<x-layout :title="$editing ? 'تعديل التصنيف' : 'إضافة تصنيف'">
    <x-breadcrumbs :items="[['label'=>'التصنيفات','url'=>route('admin.course-categories.index')],['label'=>$editing ? 'تعديل التصنيف' : 'تصنيف جديد']]" />
    <x-page-heading :title="$editing ? 'تعديل التصنيف' : 'إضافة تصنيف'" description="اختر اسمًا واضحًا وفريدًا يجمع الكورسات ذات الموضوعات المتقاربة." />
    <section class="panel max-w-3xl">
        <h2 class="panel-heading"><x-icon name="tag" :size="19" />بيانات التصنيف</h2>
        <div class="panel-body">
            <form action="{{ $editing ? route('admin.course-categories.update', $category) : route('admin.course-categories.store') }}" method="post">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif
                <div class="space-y-6">
                    <x-input name="name" label="اسم التصنيف" :value="$category->name" maxlength="120" required />
                    <x-input name="slug" label="الرابط المختصر" :value="$category->slug" maxlength="150" dir="ltr" help="اختياري؛ يُنشأ تلقائيًا من الاسم عند تركه فارغًا. يجب أن يكون فريدًا." />
                </div>
                <x-form-actions :cancel="route('admin.course-categories.index')" :label="$editing ? 'حفظ التغييرات' : 'إضافة التصنيف'" />
            </form>
        </div>
    </section>
</x-layout>
