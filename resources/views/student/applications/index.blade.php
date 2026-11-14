<x-layout title="طلباتي">
    <x-page-heading title="طلبات التسجيل الخاصة بي" description="تابع مراجعة طلبك ومهلة الحجز. تقديم الطلب وحده لا يحجز مقعدًا."><a class="button button-secondary" href="{{ route('groups.index') }}"><x-icon name="search" :size="17" />استعراض المجموعات</a></x-page-heading>
    <div class="notice notice-info"><x-icon name="info" /><p>بعد القبول المبدئي، تُمنح مهلة 48 ساعة للحضور وإتمام التسجيل لدى موظف المركز. التأكيد النهائي يتم حضوريًا، ولا توجد عملية دفع عبر المنصة.</p></div>
    @if($applications->count())
        <div class="application-list">
            @foreach($applications as $application)
                @php($state = $application->effective_status)
                <article @class(['panel','application-card','application-card-'.$state])>
                    <div class="application-card-header">
                        <div><span class="workflow-record-id">طلب #{{ $application->id }}</span><h2>{{ $application->trainingGroup?->course?->title }}</h2><div class="application-card-meta"><span>المجموعة <bdi>{{ $application->trainingGroup?->code }}</bdi></span><span>تاريخ التقديم <x-date :value="$application->created_at" date-only /></span></div></div>
                        <x-application-badge :application="$application" />
                    </div>
                    <div class="application-card-body">
                        @if($application->offer_expires_at)
                            <x-offer-deadline :application="$application" />
                        @else
                            <p class="field-help">{{ $state === 'pending' ? 'طلبك قيد المراجعة. لا يوجد مقعد محجوز قبل الموافقة.' : 'يمكنك الاطلاع على تفاصيل الطلب وسجل مراجعته.' }}</p>
                        @endif
                        <div class="application-card-actions"><a class="button button-secondary" href="{{ route('student.applications.show', $application) }}">تفاصيل الطلب<x-icon name="chevron-left" :size="15" /></a>
                            @if($application->enrollment)
                                <a class="button" href="{{ route('student.enrollments.show', $application->enrollment) }}">سجل التسجيل</a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        @if($applications->hasPages())
            <div class="pagination">{{ $applications->links() }}</div>
        @endif
    @else
        <section class="panel"><x-empty title="لم تقدّم طلب تسجيل بعد" description="اختر مجموعة منشورة واستعرض تفاصيلها ومواعيد التسجيل، ثم أرسل طلبك للمراجعة."><a class="button" href="{{ route('groups.index') }}">استعراض المجموعات</a></x-empty></section>
    @endif
</x-layout>
