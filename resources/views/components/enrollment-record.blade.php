@props(['enrollment', 'currency' => null, 'showActor' => false])
<dl class="detail-list">
    <div><dt>حالة التسجيل</dt><dd><x-badge :status="$enrollment->status" :label="$enrollment->status === 'active' ? 'تسجيل نشط' : 'منسحب'" /></dd></div>
    <div><dt>وقت التسجيل النهائي</dt><dd><x-date :value="$enrollment->enrolled_at" /></dd></div>
    <div><dt>المبلغ الموثّق</dt><dd>
        @if($enrollment->paid_amount !== null)
            {{ number_format((float)$enrollment->paid_amount, 2) }} {{ $currency }}
        @else
            غير موثّق
        @endif
    </dd></div>
    <div><dt>وقت استلام الرسوم</dt><dd>
        @if($enrollment->paid_at)
            <x-date :value="$enrollment->paid_at" />
        @elseif((string)$enrollment->paid_amount === '0.00')
            دون تحصيل رسوم
        @else
            غير مسجل
        @endif
    </dd></div>
    @if($showActor && $enrollment->relationLoaded('confirmedBy'))
        <div><dt>الموظف الذي أكد التسجيل</dt><dd>{{ $enrollment->confirmedBy?->name ?? 'غير موثّق' }}</dd></div>
    @endif
    @if($enrollment->withdrawn_at)
        <div><dt>وقت الانسحاب</dt><dd><x-date :value="$enrollment->withdrawn_at" /></dd></div>
    @endif
</dl>
