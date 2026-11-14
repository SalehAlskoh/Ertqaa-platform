@php
    $group = $application->trainingGroup;
    $state = $application->effective_status;
    $groupAcceptsConfirmation = !in_array($group->status, ['cancelled','completed','draft'], true);
@endphp
<x-layout title="مراجعة طلب التسجيل">
    <x-breadcrumbs :items="[['label'=>'طلبات التسجيل','url'=>route('admin.applications.index')],['label'=>'طلب #'.$application->id]]" />
    <x-page-heading :title="'طلب التسجيل #'.$application->id" :description="$group->course?->title.' · '.$group->code"><x-application-badge :application="$application" /></x-page-heading>
    <div class="workflow-grid">
        <div class="workflow-main">
            <section class="panel"><h2 class="panel-heading"><x-icon name="user" :size="19" />الطالب وبيانات الطلب</h2><div class="panel-body">
                <dl class="detail-list">
                    <div><dt>اسم الطالب</dt><dd>{{ $application->student?->name }}</dd></div>
                    <div><dt>البريد الإلكتروني</dt><dd><bdi>{{ $application->student?->email }}</bdi></dd></div>
                    <div><dt>رقم الهاتف</dt><dd><bdi>{{ $application->student?->studentProfile?->phone_number ?? 'غير مسجل' }}</bdi></dd></div>
                    <div><dt>وقت تقديم الطلب</dt><dd><x-date :value="$application->created_at" /></dd></div>
                    <div><dt>المجموعة</dt><dd><a class="text-link" href="{{ route('admin.training-groups.show', $group) }}"><bdi>{{ $group->code }}</bdi></a></dd></div>
                    <div><dt>رسوم المجموعة</dt><dd>
                        @if($group->fee_amount !== null)
                            {{ number_format((float)$group->fee_amount, 2) }} {{ $group->currency_code }}
                        @else
                            غير محددة
                        @endif
                    </dd></div>
                    @if($application->reviewed_at)
                        <div><dt>آخر مراجعة</dt><dd><x-date :value="$application->reviewed_at" /></dd></div>
                        <div><dt>الموظف المراجع</dt><dd>{{ $application->reviewer?->name ?? 'غير مسجل' }}</dd></div>
                    @endif
                </dl>
                @if($application->rejection_reason)
                    <div class="workflow-reason mt-6"><h3 class="field-label">سبب الرفض</h3><p class="detail-prose">{{ $application->rejection_reason }}</p></div>
                @endif
            </div></section>
            @if($application->enrollment)
                <section class="panel"><h2 class="panel-heading"><x-icon name="check-circle" :size="19" />سجل التسجيل النهائي</h2><div class="panel-body"><x-enrollment-record :enrollment="$application->enrollment" :currency="$group->currency_code" show-actor /><p class="field-help mt-6">هذا سجل محفوظ. الانسحاب لا يحذف بيانات التحصيل ولا ينفّذ استردادًا ماليًا.</p></div></section>
            @endif
        </div>
        <aside class="workflow-side">
            <section class="panel"><h2 class="panel-heading"><x-icon name="seat" :size="19" />سعة المجموعة الحالية</h2><div class="panel-body"><x-capacity-summary :capacity="$group->capacity" :occupied="$occupiedSeats" :reserved="$reservedSeats" /><p class="field-help mt-4">يشمل الحجز المؤقت العروض السارية فقط. يعيد الخادم التحقق عند كل إجراء.</p></div></section>
            @if($application->offer_expires_at)
                <x-offer-deadline :application="$application" />
            @endif
            @can('review', $application)
                @if($state === 'pending' && !$application->enrollment && $groupAcceptsConfirmation && $group->course?->is_published)
                    <section class="panel"><h2 class="panel-heading">القبول المبدئي</h2><div class="panel-body form-stack"><p class="field-help">تحجز الموافقة مقعدًا لمدة 48 ساعة. يجب الحضور لإتمام التسجيل خلال المهلة.</p>
                        <form action="{{ route('admin.applications.approve', $application) }}" method="post" data-confirm="هل تريد قبول الطلب مبدئيًا وحجز مقعد لمدة 48 ساعة؟">
                            @csrf
                            @method('PATCH')
                            <button class="button" type="submit"><x-icon name="check" :size="17" />قبول مبدئي وحجز مقعد</button>
                        </form>
                    </div></section>
                @endif
                @if(in_array($state, ['pending','awaiting_confirmation'], true) && !$application->enrollment)
                    <section class="panel"><h2 class="panel-heading">رفض الطلب</h2><div class="panel-body">
                        <form class="form-stack" action="{{ route('admin.applications.reject', $application) }}" method="post" data-confirm="هل تريد رفض الطلب وحفظ السبب؟ سيُحرر أي مقعد محجوز لهذا الطلب.">
                            @csrf
                            @method('PATCH')
                            <x-textarea name="rejection_reason" label="سبب الرفض" rows="3" maxlength="65000" required />
                            <button class="button button-danger" type="submit">رفض الطلب</button>
                        </form>
                    </div></section>
                @endif
            @endcan
            @can('confirm', $application)
                @if($state === 'awaiting_confirmation' && !$application->enrollment && $groupAcceptsConfirmation)
                    <section class="panel"><h2 class="panel-heading">التأكيد الحضوري</h2><div class="panel-body form-stack"><p class="field-help">بعد حضور الطالب، راجع الرسوم وسجّل التأكيد النهائي باسم حسابك الحالي.</p><a class="button" href="{{ route('admin.applications.confirm', $application) }}">إتمام التسجيل الحضوري<x-icon name="chevron-left" :size="16" /></a></div></section>
                @elseif($state === 'expired')
                    <div class="notice notice-info"><x-icon name="clock" /><p>انتهت مهلة العرض. لا يمكن تأكيده أو تمديده.</p></div>
                @elseif(!$groupAcceptsConfirmation && !$application->enrollment)
                    <div class="notice notice-info"><x-icon name="info" /><p>حالة المجموعة الحالية لا تسمح بتأكيد تسجيل جديد.</p></div>
                @endif
            @endcan
        </aside>
    </div>
</x-layout>
