@php
    $capacity = (int) $trainingGroup->capacity;
    $remaining = max(0, $capacity - $occupiedSeats - $reservedSeats);
    $occupiedPercent = min(100, round($occupiedSeats / max(1, $capacity) * 100, 2));
    $reservedPercent = min(100 - $occupiedPercent, round($reservedSeats / max(1, $capacity) * 100, 2));
@endphp
<x-layout title="تفاصيل المجموعة التدريبية">
    <nav class="breadcrumb" aria-label="مسار الصفحة">
        <a href="{{ route('admin.training-groups.index') }}">المجموعات التدريبية</a>
        <x-icon name="chevron-left" :size="13" />
        <span dir="ltr">{{ $trainingGroup->code }}</span>
    </nav>

    <section class="panel group-hero" aria-labelledby="group-title">
        <div class="group-hero-header">
            <div>
                <div class="group-hero-meta">
                    <x-badge :status="$trainingGroup->status" />
                    @if($trainingGroup->course?->category)
                        <span class="badge"><x-icon name="book" :size="14" />{{ $trainingGroup->course->category->name }}</span>
                    @endif
                    <span class="group-code">رمز المجموعة: <bdi>{{ $trainingGroup->code }}</bdi></span>
                </div>
                <h1 class="page-title" id="group-title">{{ $trainingGroup->course?->title ?? 'تفاصيل المجموعة' }}</h1>
                <p class="page-description">معلومات المجموعة التدريبية وإسناد المحاضر والسعة وفترة التسجيل.</p>
            </div>
            <div class="group-hero-actions">
                <a class="button" href="{{ route('admin.training-groups.edit', $trainingGroup) }}"><x-icon name="edit" :size="17" />تعديل المجموعة</a>
                @if($trainingGroup->status === 'published' && $trainingGroup->course?->is_published)
                    <a class="button button-secondary" href="{{ route('groups.show', $trainingGroup) }}"><x-icon name="eye" :size="18" />العرض العام</a>
                @endif
            </div>
        </div>
    </section>

    <section class="stats-grid" aria-label="إشغال المجموعة">
        <x-stat-card label="السعة الكلية" :value="$capacity" unit="مقعد" icon="seat" note="الحد الأقصى المحدد للمجموعة" />
        <x-stat-card label="المقاعد المشغولة" :value="$occupiedSeats" unit="مقعد" icon="check-circle" note="التسجيلات النشطة الحالية" tone="green" />
        <x-stat-card label="الحجوزات المؤقتة السارية" :value="$reservedSeats" unit="مقعد" icon="clock" note="الحجوزات التي لم تنتهِ مهلتها" tone="gold" />
        <x-stat-card label="المقاعد المتبقية" :value="$remaining" unit="مقعد" icon="users" note="بعد احتساب الإشغال والحجوزات" tone="soft" />
    </section>

    <section class="panel capacity-panel" aria-labelledby="capacity-title">
        <div class="capacity-heading">
            <div><h2 id="capacity-title">توزيع المقاعد</h2><p>{{ $occupiedSeats + $reservedSeats }} من أصل {{ $capacity }} مقعدًا مشغولًا أو محجوزًا</p></div>
            <div class="capacity-legend"><span><i class="legend-dot legend-dot-occupied" aria-hidden="true"></i>مشغول</span><span><i class="legend-dot legend-dot-reserved" aria-hidden="true"></i>محجوز مؤقتًا</span><span><i class="legend-dot" aria-hidden="true"></i>متبقٍ</span></div>
        </div>
        <div class="capacity-track" role="img" aria-label="{{ $occupiedSeats }} مقعد مشغول، {{ $reservedSeats }} محجوز مؤقتًا، {{ $remaining }} متبقٍ" style="--occupied: {{ $occupiedPercent }}%; --reserved: {{ $reservedPercent }}%;"><div class="capacity-occupied"></div><div class="capacity-reserved"></div></div>
    </section>

    <nav class="panel workflow-tools" aria-label="إدارة المجموعة">
        <a class="button button-secondary" href="{{ route('admin.lectures.index', ['group'=>$trainingGroup->id]) }}"><x-icon name="calendar" :size="17" />محاضرات المجموعة</a>
        <a class="button button-secondary" href="{{ route('admin.lectures.generate', ['group'=>$trainingGroup->id]) }}">توليد الجدول</a>
        <a class="button button-secondary" href="{{ route('admin.applications.index', ['group'=>$trainingGroup->id]) }}"><x-icon name="file" :size="17" />طلبات التسجيل</a>
        <a class="button button-secondary" href="{{ route('admin.training-groups.students', $trainingGroup) }}"><x-icon name="users" :size="17" />الطلاب المسجلون</a>
    </nav>
    <x-group-details :training-group="$trainingGroup" />
</x-layout>
