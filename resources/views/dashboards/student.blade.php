<x-layout title="لوحة الطالب">
    <div class="dashboard-page dashboard-student">
        <x-breadcrumbs :items="[['label'=>'لوحة الطالب']]" />
        <x-page-heading title="خطواتك القادمة" :description="'مرحبًا، '.auth()->user()->name.'. تابع طلباتك وتسجيلاتك، واستعد لمحاضراتك القادمة.'">
            <span class="dashboard-local-date"><x-icon name="calendar" :size="17" /><span>اليوم بتوقيت طرابلس <bdi>{{ $localDate }}</bdi></span></span>
        </x-page-heading>
        @if($validOffers->isNotEmpty())
            <div class="notice notice-info dashboard-student-alert"><x-icon name="clock" /><p>تم قبول طلبك مبدئيًا. يرجى الحضور إلى المركز لاستكمال التسجيل قبل انتهاء المهلة.</p></div>
        @endif
        <section class="stats-grid dashboard-stats" aria-label="مؤشرات الطالب الخاصة">
            <x-stat-card label="طلباتي قيد المراجعة" :value="$stats['pending_applications']" icon="file" note="طلباتك التي تنتظر المراجعة المبدئية" />
            <x-stat-card label="حجوزاتي الصالحة" :value="$stats['valid_reservations']" icon="clock" note="قبول مبدئي بمهلة سارية؛ ليس تسجيلًا نهائيًا" tone="gold" />
            <x-stat-card label="تسجيلاتي النشطة" :value="$stats['active_enrollments']" icon="check-circle" note="تسجيلاتك المؤكدة؛ دون السجلات المنسحب منها" tone="green" />
            <x-stat-card label="محاضراتي القادمة" :value="$stats['upcoming_lectures']" icon="calendar" note="مجدولة ولم تبدأ؛ من مجموعات تسجيلاتك النشطة" />
        </section>
        <x-dashboard-quick-links :links="[
            ['label'=>'تصفح الكورسات','url'=>route('courses.index'),'icon'=>'book'],
            ['label'=>'طلباتي','url'=>route('student.applications.index'),'icon'=>'file'],
            ['label'=>'تسجيلاتي','url'=>route('student.enrollments.index'),'icon'=>'check-circle'],
            ['label'=>'محاضراتي','url'=>route('student.lectures.index'),'icon'=>'calendar'],
            ['label'=>'ملفي الشخصي','url'=>route('profile.edit'),'icon'=>'user']
        ]" />
        <div class="dashboard-columns">
            <div class="dashboard-main">
                <x-dashboard-applications :applications="$myApplications" title="أحدث طلباتي" student />
                <x-dashboard-panel title="تسجيلاتي النشطة" icon="book" description="أحدث 6 تسجيلات نشطة كحد أقصى. سجل التسجيلات الكامل يشمل أيضًا السجلات المنسحب منها." :url="route('student.enrollments.index')" link-label="سجل التسجيلات">
                    @if($activeEnrollments->isNotEmpty())
                        <div class="dashboard-enrollment-list">
                            @foreach($activeEnrollments as $enrollment)
                                @php($group = $enrollment->application?->trainingGroup)
                                <article class="dashboard-enrollment-entry">
                                    <div class="dashboard-enrollment-heading"><span class="dashboard-enrollment-icon" aria-hidden="true"><x-icon name="book" :size="20" /></span><div><h3><a href="{{ route('student.enrollments.show', $enrollment) }}">{{ $group?->course?->title }}</a></h3><p class="dashboard-item-meta">المجموعة <bdi>{{ $group?->code }}</bdi> · {{ $group?->instructor?->user?->name ?? 'المحاضر غير محدد' }}</p></div><x-badge status="active" label="تسجيل نشط" /></div>
                                    <div class="dashboard-enrollment-footer"><p>تاريخ التأكيد <x-date :value="$enrollment->enrolled_at" date-only /></p><div><a class="button button-secondary button-small" href="{{ route('student.enrollments.show', $enrollment) }}">تفاصيل التسجيل</a><a class="button button-secondary button-small" href="{{ route('student.lectures.index', ['group'=>$group?->id]) }}">محاضرات المجموعة</a></div></div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <x-empty title="لا توجد تسجيلات نشطة بعد" description="يظهر التسجيل النشط هنا بعد تأكيده حضوريًا لدى المركز. يمكنك متابعة طلباتك أو مراجعة سجلك السابق." />
                    @endif
                </x-dashboard-panel>
                <x-dashboard-lectures :lectures="$upcomingLectures" title="محاضراتي القادمة" description="أقرب 6 محاضرات مجدولة لم تبدأ بعد، من المجموعات التي تملك فيها تسجيلًا نشطًا فقط." :url="route('student.lectures.index')" />
            </div>
            <aside class="dashboard-side" aria-label="حجوزاتي وإشعاراتي">
                <x-dashboard-reservations :applications="$validOffers" title="بانتظار حضورك للمركز" description="أقرب 6 حجوزات صالحة لك كحد أقصى. راجع نهاية المهلة الفعلية لكل طلب." student />
                <x-dashboard-notifications :notifications="$notifications" />
            </aside>
        </div>
    </div>
</x-layout>
