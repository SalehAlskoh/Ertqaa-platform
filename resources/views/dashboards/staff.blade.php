<x-layout title="لوحة الموظف">
    <div class="dashboard-page dashboard-staff">
        <x-breadcrumbs :items="[['label'=>'لوحة الموظف']]" />
        <x-page-heading title="المتابعة اليومية" :description="'مرحبًا، '.auth()->user()->name.'. راجع الطلبات والحجوزات، ثم تابع جدول اليوم.'">
            <span class="dashboard-local-date"><x-icon name="calendar" :size="17" /><span>اليوم بتوقيت طرابلس <bdi>{{ $localDate }}</bdi></span></span>
        </x-page-heading>
        <section class="stats-grid dashboard-stats" aria-label="مؤشرات المتابعة اليومية">
            <x-stat-card label="طلبات قيد المراجعة" :value="$stats['pending_applications']" icon="file" note="طلبات تنتظر المراجعة المبدئية" tone="green" />
            <x-stat-card label="حجوزات مؤقتة صالحة" :value="$stats['valid_reservations']" icon="seat" note="عروض سارية قبل التأكيد النهائي" tone="gold" />
            <x-stat-card label="تنتهي خلال 24 ساعة" :value="$stats['expiring_reservations']" icon="clock" note="من الحجوزات الصالحة حاليًا" tone="gold" />
            <x-stat-card label="تسجيلات أُكدت اليوم" :value="$stats['confirmed_today']" icon="check-circle" note="بحسب وقت التأكيد؛ تشمل من انسحب لاحقًا" />
            <x-stat-card label="محاضرات اليوم المجدولة" :value="$stats['today_scheduled_lectures']" icon="calendar" note="الحالة مجدولة فقط · يوم طرابلس" />
        </section>
        <x-dashboard-quick-links :links="[
            ['label'=>'مراجعة الطلبات','url'=>route('admin.applications.index'),'icon'=>'file'],
            ['label'=>'إدارة الكورسات','url'=>route('admin.courses.index'),'icon'=>'book'],
            ['label'=>'المجموعات التدريبية','url'=>route('admin.training-groups.index'),'icon'=>'users'],
            ['label'=>'إدارة المحاضرات','url'=>route('admin.lectures.index'),'icon'=>'calendar']
        ]" />
        <div class="dashboard-columns">
            <div class="dashboard-main">
                <div class="dashboard-confirmation-queue">
                    <x-dashboard-reservations :applications="$awaitingConfirmation" title="طلبات تحتاج التأكيد الحضوري" description="أقرب 6 حجوزات صالحة كحد أقصى، مرتبة حسب انتهاء المهلة. يُتم الموظف التأكيد بعد الحضور واستيفاء الرسوم." confirm />
                </div>
                <x-dashboard-applications :applications="$recentApplications" title="أحدث طلبات الطلاب" />
            </div>
            <aside class="dashboard-side" aria-label="المهل وإرشاد المتابعة">
                <x-dashboard-reservations :applications="$expiringReservations" />
                <x-dashboard-panel title="قبل التأكيد النهائي" icon="info" description="القبول المبدئي ليس تسجيلًا نهائيًا.">
                    <ol class="dashboard-follow-up">
                        <li><strong>تحقق من الحضور</strong><p>راجع الطالب والمجموعة والمهلة السارية من صفحة الطلب.</p></li>
                        <li><strong>راجع الرسوم</strong><p>تُستوفى الرسوم حضوريًا وكاملة. الرسوم غير المحددة تمنع التأكيد، والصفر الصريح تسجيل مجاني.</p></li>
                        <li><strong>احفظ التأكيد</strong><p>يسجّل الخادم حساب الموظف والوقت، ثم ينشئ التسجيل النهائي. لا تُمدد مهلة الحجز بإعادة المحاولة.</p></li>
                    </ol>
                </x-dashboard-panel>
            </aside>
        </div>
        <x-dashboard-lectures :lectures="$todayLectures" title="محاضرات اليوم" description="حتى 6 محاضرات من جميع حالات اليوم. مؤشر المتابعة يحسب المجدولة فقط." :url="route('admin.lectures.index')" />
    </div>
</x-layout>
