@php
    $group = $application->trainingGroup;
    $state = $application->effective_status;
@endphp
<x-layout title="تفاصيل طلبي">
    <x-breadcrumbs :items="[['label'=>'طلباتي','url'=>route('student.applications.index')],['label'=>'طلب #'.$application->id]]" />
    <x-page-heading :title="$group->course?->title ?? 'تفاصيل الطلب'" :description="'طلب #'.$application->id.' · المجموعة '.$group->code"><x-application-badge :application="$application" /></x-page-heading>
    <div class="workflow-grid">
        <div class="workflow-main">
            @if($state === 'awaiting_confirmation')
                <div class="notice notice-info"><x-icon name="check-circle" /><p>حصلت على قبول مبدئي وحجز مؤقت. احضر لإتمام التسجيل لدى موظف المركز قبل انتهاء المهلة. لا يُعد هذا القبول تسجيلًا نهائيًا.</p></div>
            @elseif($state === 'pending')
                <div class="notice notice-info"><x-icon name="clock" /><p>طلبك قيد المراجعة. لا يحجز تقديم الطلب مقعدًا في المجموعة.</p></div>
            @elseif($state === 'expired')
                <div class="notice notice-info"><x-icon name="clock" /><p>انتهت مهلة الحجز دون تأكيد نهائي. لم يعد المقعد محجوزًا لهذا الطلب، ولا يمكن تمديد المهلة أو تقديم طلب آخر للمجموعة نفسها.</p></div>
            @endif
            <section class="panel"><h2 class="panel-heading"><x-icon name="file" :size="19" />بيانات الطلب</h2><div class="panel-body"><dl class="detail-list">
                <div><dt>الطالب</dt><dd>{{ $application->student?->name }}</dd></div>
                <div><dt>تاريخ التقديم</dt><dd><x-date :value="$application->created_at" /></dd></div>
                <div><dt>المجموعة</dt><dd><bdi>{{ $group->code }}</bdi></dd></div>
                <div><dt>رسوم المجموعة</dt><dd>
                    @if($group->fee_amount !== null)
                        {{ number_format((float)$group->fee_amount, 2) }} {{ $group->currency_code }}
                    @else
                        غير محددة بعد
                    @endif
                </dd></div>
                @if($application->reviewed_at)
                    <div><dt>وقت المراجعة</dt><dd><x-date :value="$application->reviewed_at" /></dd></div>
                @endif
            </dl>
            @if($application->rejection_reason)
                <div class="workflow-reason mt-6"><h3 class="field-label">سبب رفض الطلب</h3><p class="detail-prose">{{ $application->rejection_reason }}</p></div>
            @endif
            </div></section>
            @if($application->enrollment)
                <section class="panel"><h2 class="panel-heading"><x-icon name="check-circle" :size="19" />تم إنشاء سجل التسجيل</h2><div class="panel-body form-stack"><p class="detail-prose">يمكنك مراجعة حالة تسجيلك وبيانات التأكيد المحفوظة من صفحة التسجيل.</p><a class="button" href="{{ route('student.enrollments.show', $application->enrollment) }}">عرض سجل التسجيل</a></div></section>
            @endif
        </div>
        <aside class="workflow-side">
            @if($application->offer_expires_at)
                <x-offer-deadline :application="$application" />
            @endif
            @if(in_array($state, ['pending','awaiting_confirmation'], true) && !$application->enrollment)
                @can('cancel', $application)
                    <section class="panel"><h2 class="panel-heading">إلغاء الطلب</h2><div class="panel-body form-stack"><p class="field-help">يمكنك إلغاء الطلب قبل التسجيل النهائي. يُحفظ السجل ويُحرر أي مقعد محجوز، ولا يتاح إرسال طلب جديد للمجموعة نفسها.</p>
                        <form action="{{ route('student.applications.cancel', $application) }}" method="post" data-confirm="هل تريد إلغاء طلبك؟ سيُحرر أي مقعد محجوز، ولن تتمكن من إرسال طلب آخر للمجموعة نفسها.">
                            @csrf
                            @method('PATCH')
                            <button class="button button-danger" type="submit">إلغاء طلبي</button>
                        </form>
                    </div></section>
                @endcan
            @endif
            <div class="group-section-note"><x-icon name="info" :size="20" /><p>تُعرض المواعيد بتوقيت طرابلس. لا يمكن للطالب تأكيد التسجيل النهائي أو الدفع إلكترونيًا.</p></div>
        </aside>
    </div>
</x-layout>
