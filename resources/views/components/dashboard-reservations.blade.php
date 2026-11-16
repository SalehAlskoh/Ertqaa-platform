@props(['applications', 'title' => 'حجوزات تنتهي خلال 24 ساعة', 'description' => 'أقرب 6 حجوزات صالحة كحد أقصى، تنتهي خلال الساعات الـ24 المقبلة.', 'student' => false, 'confirm' => false])
<x-dashboard-panel :title="$title" icon="clock" :description="$description" :url="route($student ? 'student.applications.index' : 'admin.applications.index', $student ? [] : ['status'=>'awaiting_confirmation'])" tone="gold">
    @if($applications->isNotEmpty())
        <div class="dashboard-reservation-list">
            @foreach($applications as $application)
                <article class="dashboard-reservation">
                    @if(!$student)<h3>{{ $application->student?->name }}</h3>@endif
                    <p class="dashboard-item-title">{{ $application->trainingGroup?->course?->title }}</p>
                    <p class="dashboard-item-meta">المجموعة <bdi>{{ $application->trainingGroup?->code }}</bdi> · طلب #{{ $application->id }}</p>
                    <x-offer-deadline :application="$application" />
                    <div class="dashboard-item-actions">
                        <a class="text-link" href="{{ route($student ? 'student.applications.show' : 'admin.applications.show', $application) }}">تفاصيل الطلب<x-icon name="chevron-left" :size="14" /></a>
                        @if($confirm && $application->effective_status === 'awaiting_confirmation' && !$application->enrollment)
                            @can('confirm', $application)
                                <a class="button button-small" href="{{ route('admin.applications.confirm', $application) }}">التأكيد الحضوري</a>
                            @endcan
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <x-empty title="لا توجد حجوزات ضمن هذا النطاق" description="العروض المنتهية لا تُحسب حجوزات صالحة ولا تظهر في هذه القائمة." />
    @endif
</x-dashboard-panel>
