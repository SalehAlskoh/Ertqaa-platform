@props(['announcement'])
<span {{ $attributes->class(['announcement-audience']) }}>
    @if($announcement->course_id)
        <x-icon name="book" :size="16" /><span>طلاب كورس {{ $announcement->course?->title ?? 'غير متاح' }}</span>
    @else
        <x-icon name="users" :size="16" /><span>المجموعة <bdi>{{ $announcement->trainingGroup?->code ?? 'غير متاحة' }}</bdi>@if($announcement->trainingGroup?->course) · {{ $announcement->trainingGroup->course->title }}@endif</span>
    @endif
</span>
