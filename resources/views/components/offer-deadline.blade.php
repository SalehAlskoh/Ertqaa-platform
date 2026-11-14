@props(['application', 'compact' => false])
@php
    $deadline = $application->offer_expires_at;
    $remainingSeconds = $deadline ? max(0, $deadline->getTimestamp() - time()) : 0;
    $expired = $application->effective_status === 'expired';
@endphp
@if($deadline)
    <div @class(['offer-deadline', 'offer-deadline-compact' => $compact, 'offer-deadline-expired' => $expired])>
        <span class="offer-deadline-label">{{ $expired ? 'انتهت مهلة الحجز' : 'نهاية مهلة الحجز' }}</span>
        <x-date :value="$deadline" />
        @if($application->effective_status === 'awaiting_confirmation' && $remainingSeconds > 0)
            <p class="offer-remaining">
                @if($remainingSeconds < 60)
                    متبقي أقل من دقيقة
                @else
                    متبقي {{ intdiv($remainingSeconds, 3600) }} ساعة و{{ intdiv($remainingSeconds % 3600, 60) }} دقيقة
                @endif
            </p>
        @elseif($expired)
            <p class="offer-remaining">لا يمكن تأكيد هذا العرض بعد انتهاء المهلة.</p>
        @endif
    </div>
@else
    <span class="field-help">لا توجد مهلة حجز</span>
@endif
