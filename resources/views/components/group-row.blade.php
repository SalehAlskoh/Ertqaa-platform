@props(['group', 'routeName' => 'groups.show', 'course' => null])
@php($groupCourse = $course ?? ($group->relationLoaded('course') ? $group->course : null))
<article class="group-entry">
    <div class="group-entry-content">
        <div class="group-entry-heading"><span class="group-entry-code"><bdi>{{ $group->code }}</bdi></span><x-badge :status="$group->status" /></div>
        <h3>{{ $groupCourse?->title ?? 'المجموعة '.$group->code }}</h3>
        <div class="group-entry-meta">
            <span><x-icon name="user" :size="15" />{{ $group->instructor?->user?->name ?? 'غير محدد' }}</span>
            <span><x-icon name="seat" :size="15" />{{ $group->capacity }} مقعدًا</span>
        </div>
    </div>
    <div class="group-entry-action">
        @if($group->fee_amount !== null)
            <p class="group-entry-fee">{{ number_format((float)$group->fee_amount, 2) }}<span>{{ $group->currency_code }}</span></p>
        @endif
        <a class="button button-secondary button-small" href="{{ route($routeName, $group) }}">تفاصيل المجموعة <x-icon name="arrow-left" :size="15" /></a>
    </div>
</article>
