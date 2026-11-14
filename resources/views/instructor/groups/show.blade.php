<x-layout title="تفاصيل مجموعتي">
    <x-breadcrumbs :items="[['label'=>'مجموعاتي','url'=>route('instructor.groups.index')],['label'=>$trainingGroup->code]]" />
    <section class="panel group-hero" aria-labelledby="my-group-title">
        <div class="group-hero-header">
            <div>
                <div class="group-hero-meta"><x-badge :status="$trainingGroup->status" /><span class="group-code">رمز المجموعة: <bdi>{{ $trainingGroup->code }}</bdi></span></div>
                <h1 class="page-title" id="my-group-title">{{ $trainingGroup->course?->title ?? 'تفاصيل المجموعة' }}</h1>
                <p class="page-description">بيانات المجموعة التدريبية المسندة إليك. المواعيد بتوقيت طرابلس.</p>
            </div>
            <a class="button button-secondary" href="{{ route('instructor.groups.index') }}"><x-icon name="arrow-right" :size="17" />العودة إلى مجموعاتي</a>
        </div>
    </section>
    <nav class="panel workflow-tools" aria-label="متابعة المجموعة">
        <a class="button button-secondary" href="{{ route('instructor.lectures.index', ['group'=>$trainingGroup->id]) }}"><x-icon name="calendar" :size="17" />محاضرات المجموعة</a>
        <a class="button button-secondary" href="{{ route('instructor.groups.students', $trainingGroup) }}"><x-icon name="users" :size="17" />الطلاب المسجلون</a>
    </nav>
    <x-group-details :training-group="$trainingGroup" />
</x-layout>
