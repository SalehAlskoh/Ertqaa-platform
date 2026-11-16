@php($editing = $announcement->exists)
<x-layout :title="$editing ? 'تعديل الإعلان العام' : 'إعلان عام جديد'">
    <x-breadcrumbs :items="[['label'=>'إعلانات المركز','url'=>route('admin.announcements.index')],['label'=>$editing ? 'تعديل الإعلان' : 'إعلان جديد']]" />
    <x-page-heading :title="$editing ? 'تعديل الإعلان العام' : 'إضافة إعلان عام'" description="اكتب إعلانًا واضحًا للمركز. النشر وإلغاء النشر إجراءان مستقلان بعد الحفظ." />
    <x-announcement-form :announcement="$announcement" />
</x-layout>
