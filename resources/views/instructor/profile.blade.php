<x-layout title="ملفي المهني">
    <section class="panel group-hero">
        <div class="group-hero-header">
            <div class="instructor-hero-summary">
                <span class="account-avatar" aria-hidden="true">{{ mb_substr($instructor->user?->name ?? 'م', 0, 1) }}</span>
                <div><p class="eyebrow">مساحة المحاضر</p><h1>ملفي المهني</h1><p>{{ $instructor->user?->name }}</p></div>
            </div>
            <a class="button button-secondary" href="{{ route('instructor.groups.index') }}"><x-icon name="users" :size="18" />مجموعاتي التدريبية</a>
        </div>
    </section>
    <div class="detail-layout">
        <section class="panel">
            <h2 class="panel-heading"><x-icon name="briefcase" :size="19" />المعلومات المهنية</h2>
            <div class="panel-body">
                <dl class="professional-facts"><div class="professional-fact"><dt>المسمى المهني</dt><dd>{{ $instructor->professional_title ?: 'غير محدد' }}</dd></div><div class="professional-fact"><dt>سنوات الخبرة</dt><dd>{{ $instructor->experience_years ?? 'غير محددة' }}</dd></div></dl>
                <h3 class="form-section-title">النبذة المهنية</h3>
                <p class="detail-prose">{{ $instructor->biography ?: 'لم تتم إضافة نبذة مهنية بعد.' }}</p>
            </div>
        </section>
        <aside class="panel">
            <h2 class="panel-heading"><x-icon name="info" :size="19" />حول ملفك المهني</h2>
            <div class="panel-body">
                <p class="detail-prose">قد تُعرض المعلومات المهنية للزوار ضمن تفاصيل المجموعات. لتحديثها، تواصل مع إدارة المركز.</p>
                <a class="button button-secondary w-full mt-6" href="{{ route('profile.edit') }}"><x-icon name="user" :size="17" />حسابي وكلمة المرور</a>
            </div>
        </aside>
    </div>
</x-layout>
