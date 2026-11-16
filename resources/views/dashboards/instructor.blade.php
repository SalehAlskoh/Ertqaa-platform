<x-layout title="لوحة المحاضر">
    <div class="dashboard-page dashboard-instructor">
        <x-breadcrumbs :items="[['label'=>'لوحة المحاضر']]" />
        <x-page-heading title="مجموعاتك ومحاضراتك" :description="'مرحبًا، '.auth()->user()->name.'. تابع مجموعاتك وطلابها وجدول محاضراتك القادم.'">
            <span class="dashboard-local-date"><x-icon name="calendar" :size="17" /><span>اليوم بتوقيت طرابلس <bdi>{{ $localDate }}</bdi></span></span>
        </x-page-heading>
        <section class="stats-grid dashboard-stats" aria-label="مؤشرات مجموعات المحاضر">
            <x-stat-card label="مجموعاتي المسندة" :value="$stats['assigned_groups']" icon="users" note="كل المجموعات المسندة إليك بجميع حالاتها" />
            <x-stat-card label="طلاب نشطون دون تكرار" :value="$stats['active_students']" icon="user" note="يُحسب الطالب مرة واحدة عبر تسجيلاته النشطة في مجموعاتك" tone="green" />
            <x-stat-card label="محاضراتي القادمة" :value="$stats['upcoming_lectures']" icon="calendar" note="محاضرات مجدولة لم تبدأ بعد، من مجموعاتك" />
            <x-stat-card label="محاضرات اليوم المجدولة" :value="$stats['today_scheduled_lectures']" icon="clock" note="محاضرات مجموعاتك المجدولة في يوم طرابلس" tone="soft" />
        </section>
        <x-dashboard-quick-links :links="[
            ['label'=>'جميع مجموعاتي','url'=>route('instructor.groups.index'),'icon'=>'users'],
            ['label'=>'محاضرات مجموعاتي','url'=>route('instructor.lectures.index'),'icon'=>'calendar'],
            ['label'=>'إشعاراتي','url'=>route('notifications.index'),'icon'=>'bell'],
            ['label'=>'ملفي المهني','url'=>route('instructor.profile'),'icon'=>'briefcase']
        ]" />
        <div class="dashboard-columns">
            <div class="dashboard-main">
                @if($myGroups->isNotEmpty())
                    <x-data-table title="مجموعاتي" label="أحدث 6 مجموعات مسندة إليك كحد أقصى" class="dashboard-group-table">
                        <x-slot:head><tr><th scope="col">الكورس والمجموعة</th><th scope="col">الحالة</th><th scope="col">طلاب بتسجيل نشط</th><th scope="col">المتابعة</th></tr></x-slot:head>
                        @foreach($myGroups as $group)
                            <tr>
                                <td class="dashboard-course-cell"><strong class="table-primary">{{ $group->course?->title }}</strong><p class="table-secondary"><bdi>{{ $group->code }}</bdi></p></td>
                                <td><x-badge :status="$group->status" /></td>
                                <td><strong class="dashboard-count">{{ $group->occupied_seats }}</strong></td>
                                <td><div class="row-actions"><a class="button button-secondary button-small" href="{{ route('instructor.groups.show', $group) }}">التفاصيل</a><a class="button button-secondary button-small" href="{{ route('instructor.groups.students', $group) }}">الطلاب المسجلون</a></div></td>
                            </tr>
                        @endforeach
                        <x-slot:pagination>
                            <div class="dashboard-table-footer"><span>أحدث 6 مجموعات كحد أقصى؛ عدد الطلاب لكل مجموعة يقتصر على التسجيل النشط.</span><a class="text-link" href="{{ route('instructor.groups.index') }}">جميع مجموعاتي<x-icon name="arrow-left" :size="14" /></a></div>
                        </x-slot:pagination>
                    </x-data-table>
                @else
                    <x-dashboard-panel title="مجموعاتي" icon="users" description="المجموعات المسندة إلى حسابك فقط." :url="route('instructor.groups.index')"><x-empty title="لم تُسند إليك مجموعات بعد" description="ستظهر مجموعاتك وطلابها المسجلون هنا بعد إسنادها إليك." /></x-dashboard-panel>
                @endif
                <x-dashboard-lectures :lectures="$upcomingLectures" title="محاضراتي القادمة" description="أقرب 6 محاضرات مجدولة كحد أقصى من مجموعاتك، لم تبدأ بعد." :url="route('instructor.lectures.index')" :show-instructor="false" />
            </div>
            <aside class="dashboard-side" aria-label="إشعارات المحاضر">
                <x-dashboard-notifications :notifications="$notifications" />
            </aside>
        </div>
    </div>
</x-layout>
