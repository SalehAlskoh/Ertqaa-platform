<x-layout title="تفاصيل مجموعتي">
    <nav class="breadcrumb" aria-label="مسار الصفحة"><a href="{{ route('instructor.groups.index') }}">مجموعاتي</a><span aria-hidden="true">/</span><span dir="ltr">{{ $trainingGroup->code }}</span></nav>
    <x-page-heading :title="$trainingGroup->course?->title ?? 'تفاصيل المجموعة'" :eyebrow="'المجموعة '.$trainingGroup->code" description="تفاصيل المجموعة التدريبية المسندة إليك، بتوقيت طرابلس." />
    <x-group-details :training-group="$trainingGroup" />
</x-layout>
