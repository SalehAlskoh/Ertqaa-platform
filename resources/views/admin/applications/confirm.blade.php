@php
    $group = $application->trainingGroup;
    $fee = $group->fee_amount;
    $free = $fee !== null && (string)$fee === '0.00';
    $validOffer = $application->effective_status === 'awaiting_confirmation' && !$application->enrollment && !in_array($group->status, ['cancelled','completed','draft'], true);
    $feeReady = $fee !== null && ($free || preg_match('/^[A-Z]{3}$/D', (string)$group->currency_code));
@endphp
<x-layout title="تأكيد التسجيل الحضوري">
    <x-breadcrumbs :items="[['label'=>'طلبات التسجيل','url'=>route('admin.applications.index')],['label'=>'طلب #'.$application->id,'url'=>route('admin.applications.show', $application)],['label'=>'التأكيد الحضوري']]" />
    <x-page-heading title="تأكيد التسجيل الحضوري" description="تحقق من هوية الطالب والمجموعة والرسوم قبل حفظ التسجيل النهائي."><x-application-badge :application="$application" /></x-page-heading>
    <div class="workflow-grid">
        <div class="workflow-main">
            <section class="panel"><h2 class="panel-heading"><x-icon name="check-circle" :size="19" />مراجعة بيانات التأكيد</h2><div class="panel-body">
                <dl class="confirmation-overview">
                    <div><dt>الطالب</dt><dd>{{ $application->student?->name }}</dd></div>
                    <div><dt>الطلب</dt><dd>#{{ $application->id }}</dd></div>
                    <div><dt>الكورس</dt><dd>{{ $group->course?->title }}</dd></div>
                    <div><dt>المجموعة</dt><dd><bdi>{{ $group->code }}</bdi></dd></div>
                    <div><dt>الموظف المنفّذ</dt><dd>{{ auth()->user()->name }}</dd></div>
                    <div><dt>وقت التأكيد</dt><dd>يحدده الخادم عند الحفظ</dd></div>
                </dl>
                @if(!$validOffer)
                    <div class="notice notice-info"><x-icon name="info" /><p>هذا الطلب لا يملك حجزًا صالحًا للتأكيد الآن. <a class="text-link" href="{{ route('admin.applications.show', $application) }}">العودة إلى الطلب</a></p></div>
                @elseif(!$feeReady)
                    <div class="notice notice-info"><x-icon name="info" /><p>{{ $fee === null ? 'رسوم المجموعة غير محددة. لا يُعتبر غياب الرسوم تسجيلًا مجانيًا.' : 'رمز عملة الرسوم غير صحيح أو غير محدد.' }} حدّث بيانات المجموعة أولًا. <a class="text-link" href="{{ route('admin.training-groups.edit', $group) }}">ضبط الرسوم</a></p></div>
                @else
                    @can('confirm', $application)
                        <div class="payment-amount-card"><span>{{ $free ? 'تسجيل مجاني' : 'إجمالي الرسوم المستحقة' }}</span><strong>{{ number_format((float)$fee, 2) }} <small>{{ $group->currency_code }}</small></strong><p>{{ $free ? 'يُحفظ التسجيل دون تحصيل رسوم ودون وقت استلام مالي.' : 'يجب استلام المبلغ كاملًا حضوريًا. لا تتوفر مدفوعات إلكترونية أو أقساط أو خصومات.' }}</p></div>
                        <form class="form-stack" action="{{ route('admin.applications.confirm.store', $application) }}" method="post" data-confirm="{{ $free ? 'هل تؤكد تسجيل الطالب نهائيًا دون تحصيل رسوم؟' : 'هل تؤكد حضور الطالب واستلام كامل الرسوم؟ سيُحفظ المبلغ باسم حسابك ووقت الخادم.' }}">
                            @csrf
                            @if($free)
                                <div class="field">
                                    <label class="field-label" for="paid_amount">المبلغ الموثق · تسجيل مجاني</label>
                                    <input class="input" id="paid_amount" name="paid_amount" type="number" value="0.00" step="0.01" dir="ltr" readonly required aria-describedby="paid_amount-help{{ $errors->has('paid_amount') ? ' paid_amount-error' : '' }}" @if($errors->has('paid_amount')) aria-invalid="true" @endif>
                                    <p class="field-help" id="paid_amount-help">القيمة صفر؛ لا يُسجّل وقت تحصيل رسوم.</p>
                                    @error('paid_amount')<p class="field-error" id="paid_amount-error">{{ $message }}</p>@enderror
                                </div>
                            @else
                                <x-input name="paid_amount" label="المبلغ المستلم حضوريًا" :value="$fee" type="number" min="0" max="99999999.99" step="0.01" dir="ltr" inputmode="decimal" required help="أدخل كامل رسوم المجموعة بالعملة المعروضة أعلاه. يرفض الخادم أي مبلغ مختلف." />
                            @endif
                            <div class="form-actions"><x-submit label="تأكيد التسجيل النهائي" icon="check-circle" /><a class="button button-secondary" href="{{ route('admin.applications.show', $application) }}">العودة دون تأكيد</a></div>
                        </form>
                    @endcan
                @endif
            </div></section>
        </div>
        <aside class="workflow-side"><x-offer-deadline :application="$application" /><section class="panel"><h2 class="panel-heading">المقاعد الحالية</h2><div class="panel-body"><x-capacity-summary :capacity="$group->capacity" :occupied="$occupiedSeats" :reserved="$reservedSeats" /><p class="field-help mt-4">يتحقق النظام مجددًا من صلاحية الحجز والمقعد والرسوم عند الحفظ.</p></div></section></aside>
    </div>
</x-layout>
