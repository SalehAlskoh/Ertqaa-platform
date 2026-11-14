@php
    $adminArea = ($area ?? 'instructor') === 'admin';
    $groupRoute = $adminArea ? 'admin.training-groups.show' : 'instructor.groups.show';
@endphp
<x-layout title="طلاب المجموعة">
    <x-breadcrumbs :items="[['label'=>$adminArea ? 'المجموعات التدريبية' : 'مجموعاتي','url'=>route($adminArea ? 'admin.training-groups.index' : 'instructor.groups.index')],['label'=>$trainingGroup->code,'url'=>route($groupRoute, $trainingGroup)],['label'=>'الطلاب المسجلون']]" />
    <x-page-heading title="طلاب المجموعة" :description="$trainingGroup->course?->title.' · '.$trainingGroup->code"><a class="button button-secondary" href="{{ route($groupRoute, $trainingGroup) }}">تفاصيل المجموعة</a></x-page-heading>
    @if(!$adminArea)
        <div class="notice notice-info"><x-icon name="users" /><p>تعرض القائمة الطلاب ذوي التسجيل النشط في مجموعتك فقط.</p></div>
    @endif
    @if($enrollments->count())
        <x-data-table :title="$adminArea ? 'سجل طلاب المجموعة' : 'الطلاب ذوو التسجيل النشط'" :count="$enrollments->total()">
            <x-slot:head><tr><th scope="col">الطالب</th><th scope="col">حالة التسجيل</th><th scope="col">تاريخ التسجيل · طرابلس</th>
                @if($adminArea)
                    <th scope="col">الطلب</th>
                @endif
            </tr></x-slot:head>
            @foreach($enrollments as $enrollment)
                <tr><td><strong class="table-primary">{{ $enrollment->application?->student?->name }}</strong></td><td><x-badge :status="$enrollment->status" :label="$enrollment->status === 'active' ? 'تسجيل نشط' : 'منسحب'" /></td><td><x-date :value="$enrollment->enrolled_at" /></td>
                    @if($adminArea)
                        <td><a class="button button-secondary button-small" href="{{ route('admin.applications.show', $enrollment->application) }}">طلب #{{ $enrollment->application?->id }}</a></td>
                    @endif
                </tr>
            @endforeach
            @if($enrollments->hasPages())
                <x-slot:pagination>{{ $enrollments->links() }}</x-slot:pagination>
            @endif
        </x-data-table>
    @else
        <section class="panel"><x-empty title="لا يوجد طلاب مسجلون للعرض" :description="$adminArea ? 'ستظهر التسجيلات النهائية للمجموعة هنا بعد التأكيد الحضوري.' : 'ستظهر أسماء الطلاب هنا بعد تأكيد تسجيلاتهم، ما دامت التسجيلات نشطة.'" /></section>
    @endif
</x-layout>
