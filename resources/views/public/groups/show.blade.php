<x-layout :title="$trainingGroup->course?->title ?? 'تفاصيل المجموعة'">
    <nav class="breadcrumb" aria-label="مسار الصفحة"><a href="{{ route('groups.index') }}">المجموعات التدريبية</a><span aria-hidden="true">/</span><span dir="ltr">{{ $trainingGroup->code }}</span></nav>
    <x-page-heading :title="$trainingGroup->course?->title ?? 'المجموعة التدريبية'" :eyebrow="'المجموعة '.$trainingGroup->code" description="معلومات المجموعة ومواعيد التسجيل معروضة بتوقيت طرابلس."><a class="button button-secondary" href="{{ route('courses.show', $trainingGroup->course->slug) }}">تفاصيل الكورس</a></x-page-heading>
    <x-group-details :training-group="$trainingGroup" />
</x-layout>
