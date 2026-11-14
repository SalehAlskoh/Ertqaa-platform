@props(['trainingGroup'])
<div class="group-detail-grid">
    <aside class="group-detail-side">
        <section class="panel">
            <h2 class="panel-heading"><x-icon name="info" :size="19" />المعلومات الأساسية</h2>
            <div class="panel-body">
                <div class="instructor-mini">
                    <span class="instructor-initial" aria-hidden="true">{{ mb_substr($trainingGroup->instructor?->user?->name ?? 'م', 0, 1) }}</span>
                    <div><p>المحاضر</p><h3>{{ $trainingGroup->instructor?->user?->name ?? 'غير محدد' }}</h3>
                        @if($trainingGroup->instructor?->professional_title)
                            <p>{{ $trainingGroup->instructor->professional_title }}</p>
                        @endif
                    </div>
                </div>
                <dl class="logistic-list">
                    <div class="logistic-item"><x-icon name="tag" :size="19" /><div><dt>الرسوم المعروضة</dt><dd class="logistic-fee">
                        @if($trainingGroup->fee_amount !== null)
                            {{ number_format((float)$trainingGroup->fee_amount, 2) }} <span class="text-xs font-semibold">{{ $trainingGroup->currency_code }}</span>
                        @else
                            <span class="text-base">غير محددة</span>
                        @endif
                    </dd><p class="field-help mt-1">تُستوفى الرسوم حضوريًا عند التأكيد النهائي. لا توجد مدفوعات إلكترونية.</p></div></div>
                    <div class="logistic-item"><x-icon name="calendar" :size="19" /><div><dt>فتح التسجيل · توقيت طرابلس</dt><dd><x-date :value="$trainingGroup->registration_opens_at" /></dd></div></div>
                    <div class="logistic-item"><x-icon name="calendar" :size="19" /><div><dt>إغلاق التسجيل · توقيت طرابلس</dt><dd><x-date :value="$trainingGroup->registration_closes_at" /></dd></div></div>
                    <div class="logistic-item"><x-icon name="seat" :size="19" /><div><dt>السعة الكلية</dt><dd>{{ $trainingGroup->capacity }} مقعدًا</dd></div></div>
                </dl>
                <div class="notice notice-info"><x-icon name="info" :size="17" /><span>القبول المبدئي يحجز المقعد مؤقتًا. التأكيد النهائي لدى موظف مخوّل في المركز.</span></div>
            </div>
        </section>
    </aside>
    <div class="group-detail-main">
        <section class="panel">
            <h2 class="panel-heading"><x-icon name="book" :size="19" />عن الكورس والمجموعة</h2>
            <div class="panel-body">
                <p class="detail-prose">{{ $trainingGroup->course?->description ?: 'لم تُضف تفاصيل إضافية للكورس بعد.' }}</p>
                <dl class="detail-list mt-6">
                    <div><dt>الكورس</dt><dd>{{ $trainingGroup->course?->title }}</dd></div>
                    <div><dt>التصنيف</dt><dd>{{ $trainingGroup->course?->category?->name ?? 'غير محدد' }}</dd></div>
                    <div><dt>رمز المجموعة</dt><dd><bdi>{{ $trainingGroup->code }}</bdi></dd></div>
                    <div><dt>حالة المجموعة</dt><dd><x-badge :status="$trainingGroup->status" /></dd></div>
                </dl>
            </div>
        </section>
        <section class="panel">
            <h2 class="panel-heading"><x-icon name="user" :size="19" />نبذة عن المحاضر</h2>
            <div class="panel-body">
                <p class="detail-prose">{{ $trainingGroup->instructor?->biography ?: 'لم تتم إضافة نبذة مهنية بعد.' }}</p>
                @if($trainingGroup->instructor?->experience_years !== null)
                    <p class="field-help mt-4">سنوات الخبرة: <strong class="text-forest">{{ $trainingGroup->instructor->experience_years }}</strong></p>
                @endif
            </div>
        </section>
        <div class="group-section-note"><x-icon name="info" :size="20" /><p>بعد الموافقة المبدئية، يجب إتمام التسجيل حضوريًا خلال مهلة الحجز المعروضة في الطلب. تنتهي الحجوزات غير المؤكدة تلقائيًا دون تسجيل نهائي.</p></div>
    </div>
</div>
