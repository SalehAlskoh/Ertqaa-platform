<x-layout title="لوحة تحكم المدير">
    <div class="dashboard-page dashboard-admin">
        <x-breadcrumbs :items="[['label'=>'لوحة تحكم المدير']]" />
        <x-page-heading title="لوحة تحكم المدير" :description="'مرحبًا، '.auth()->user()->name.'. نظرة على المركز، ثم الوصول إلى ما يحتاج المتابعة.'">
            <span class="dashboard-local-date"><x-icon name="calendar" :size="17" /><span>اليوم بتوقيت طرابلس <bdi>{{ $localDate }}</bdi></span></span>
        </x-page-heading>
        <x-dashboard-quick-links :links="[
            ['label'=>'إنشاء كورس','url'=>route('admin.courses.create'),'icon'=>'plus'],
            ['label'=>'إنشاء مجموعة','url'=>route('admin.training-groups.create'),'icon'=>'users'],
            ['label'=>'مراجعة الطلبات','url'=>route('admin.applications.index'),'icon'=>'file'],
            ['label'=>'إدارة المحاضرات','url'=>route('admin.lectures.index'),'icon'=>'calendar'],
            ['label'=>'المحاضرون','url'=>route('admin.instructors.index'),'icon'=>'user'],
            ['label'=>'الموظفون','url'=>route('admin.staff.index'),'icon'=>'briefcase']
        ]" />
        <section class="stats-grid dashboard-stats" aria-label="مؤشرات المركز">
            <x-stat-card label="حسابات الطلاب النشطة" :value="$stats['active_students']" icon="users" note="حسابات بدور طالب؛ ليست عدد التسجيلات" />
            <x-stat-card label="الكورسات المنشورة" :value="$stats['published_courses']" icon="book" note="الكورسات المنشورة في الدليل" />
            <x-stat-card label="إجمالي المجموعات" :value="$stats['groups_total']" icon="grid" note="جميع الحالات؛ التفصيل أدناه" />
            <x-stat-card label="المحاضرون النشطون" :value="$stats['active_instructors']" icon="user" note="حسابات نشطة تحمل دور محاضر" />
            <x-stat-card label="طلبات قيد المراجعة" :value="$stats['pending_applications']" icon="file" note="طلبات أولية لا تحجز مقعدًا" tone="green" />
            <x-stat-card label="حجوزات مؤقتة صالحة" :value="$stats['valid_reservations']" icon="clock" note="عروض سارية قبل التسجيل النهائي" tone="gold" />
            <x-stat-card label="تسجيلات نشطة" :value="$stats['active_enrollments']" icon="check-circle" note="تسجيلات مؤكدة؛ دون المنسحبين" tone="green" />
            <x-stat-card label="محاضرات اليوم المجدولة" :value="$stats['today_scheduled_lectures']" icon="calendar" note="الحالة مجدولة فقط · يوم طرابلس" />
        </section>
        <div class="dashboard-columns">
            <div class="dashboard-main">
                <x-dashboard-applications :applications="$recentApplications" />
                <x-dashboard-lectures :lectures="$todayLectures" title="محاضرات اليوم" description="حتى 6 محاضرات من جميع حالات اليوم. المؤشر أعلاه يحسب المجدولة فقط." :url="route('admin.lectures.index')" />
            </div>
            <aside class="dashboard-side" aria-label="متابعة الحجوزات والمجموعات">
                <x-dashboard-reservations :applications="$expiringReservations" />
                <x-dashboard-panel title="المجموعات بحسب الحالة" icon="grid" description="أعداد جميع المجموعات؛ بما فيها الحالات ذات العدد صفر." :url="route('admin.training-groups.index')">
                    <dl class="dashboard-status-list">
                        @foreach(['draft'=>'مسودة','published'=>'منشورة','in_progress'=>'قيد التنفيذ','completed'=>'مكتملة','cancelled'=>'ملغاة'] as $state=>$label)
                            <div><dt><x-badge :status="$state" :label="$label" /></dt><dd><a href="{{ route('admin.training-groups.index', ['status'=>$state]) }}" aria-label="{{ $label }}: {{ $groupStatuses[$state] }} مجموعة">{{ $groupStatuses[$state] }}</a></dd></div>
                        @endforeach
                    </dl>
                </x-dashboard-panel>
                <x-dashboard-panel title="مجموعات قاربت على الامتلاء" icon="seat" description="حتى 6 مجموعات منشورة أو قيد التنفيذ، بإشغال وحجز صالح يبلغ 80% فأكثر." :url="route('admin.training-groups.index')">
                    @if($nearCapacityGroups->isNotEmpty())
                        <div class="dashboard-capacity-list">
                            @foreach($nearCapacityGroups as $group)
                                <article><h3><a href="{{ route('admin.training-groups.show', $group) }}">{{ $group->course?->title }}</a></h3><p class="dashboard-item-meta"><bdi>{{ $group->code }}</bdi> · <x-badge :status="$group->status" /></p><x-capacity-summary :capacity="$group->capacity" :occupied="$group->occupied_seats" :reserved="$group->reserved_seats" /><a class="text-link dashboard-capacity-link" href="{{ route('admin.training-groups.show', $group) }}">تفاصيل المجموعة<x-icon name="chevron-left" :size="14" /></a></article>
                            @endforeach
                        </div>
                    @else
                        <x-empty title="لا توجد مجموعات قاربت على الامتلاء" description="تظهر هنا المجموعات المشمولة عندما تبلغ عتبة 80% من السعة." />
                    @endif
                </x-dashboard-panel>
            </aside>
        </div>
        <x-dashboard-panel title="سجل المبالغ الموثقة" icon="file" description="أحدث 6 سجلات موثقة كحد أقصى؛ ليست أرباحًا صافية. الانسحاب لا يعني استرداد الرسوم." class="dashboard-ledger">
            @if($recordedPayments->isNotEmpty())
                <div class="table-scroll" role="region" tabindex="0" aria-label="سجل المبالغ الموثقة، قابل للتمرير أفقيًا">
                    <table class="data-table dashboard-data-table">
                        <caption class="visually-hidden">المبالغ الموثقة بعملة كل مجموعة دون جمع العملات، وتشمل التسجيلات المنسحب منها</caption>
                        <thead><tr><th scope="col">الطالب والتسجيل</th><th scope="col">الكورس والمجموعة</th><th scope="col">المبلغ الموثق</th><th scope="col">وقت التحصيل · طرابلس</th><th scope="col">الموظف المؤكّد</th><th scope="col">حالة التسجيل</th></tr></thead>
                        <tbody>
                            @foreach($recordedPayments as $enrollment)
                                @php($group = $enrollment->application?->trainingGroup)
                                <tr><td><strong class="table-primary">{{ $enrollment->application?->student?->name }}</strong><p class="table-secondary"><a class="text-link" href="{{ route('admin.applications.show', $enrollment->application) }}">تسجيل #{{ $enrollment->id }}</a></p></td><td class="dashboard-course-cell"><strong class="table-primary">{{ $group?->course?->title }}</strong><p class="table-secondary"><bdi>{{ $group?->code }}</bdi></p></td><td><strong class="dashboard-amount">{{ number_format((float)$enrollment->paid_amount, 2) }}</strong> <bdi>{{ $group?->currency_code }}</bdi></td><td>
                                    @if($enrollment->paid_at)
                                        <x-date :value="$enrollment->paid_at" />
                                    @elseif((string)$enrollment->paid_amount === '0.00')
                                        مجاني؛ دون تحصيل
                                    @else
                                        غير مسجل
                                    @endif
                                </td><td>{{ $enrollment->confirmedBy?->name ?? 'غير موثق' }}</td><td><x-badge :status="$enrollment->status" :label="$enrollment->status === 'active' ? 'تسجيل نشط' : 'منسحب'" /></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <x-empty title="لا توجد مبالغ موثقة للعرض" description="تظهر سجلات التأكيد الحضوري الموثقة هنا، بما فيها التسجيل المجاني بقيمة صفر، دون تخمين بيانات تاريخية ناقصة." />
            @endif
        </x-dashboard-panel>
    </div>
</x-layout>
