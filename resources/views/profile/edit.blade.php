<x-layout title="حسابي">
    <x-page-heading title="حسابي" eyebrow="مساحتي الشخصية" description="راجع بياناتك الشخصية وحدّث معلومات التواصل وكلمة المرور بأمان." />

    <section class="panel profile-overview" aria-label="ملخص حسابي">
        <div class="profile-identity"><span class="account-avatar" aria-hidden="true">{{ mb_substr($user->name, 0, 1) }}</span><div><h2>{{ $user->name }}</h2><p><bdi>{{ $user->email }}</bdi></p></div></div>
        <nav class="profile-section-links" aria-label="أقسام الحساب"><a href="#personal-details"><x-icon name="user" :size="16" />المعلومات الشخصية</a><a href="#account-password"><x-icon name="lock" :size="16" />كلمة المرور</a></nav>
    </section>

    <div class="profile-grid">
        <section class="panel" id="personal-details" aria-labelledby="personal-details-title">
            <h2 class="panel-heading" id="personal-details-title"><x-icon name="user" :size="19" />المعلومات الشخصية</h2>
            <div class="panel-body">
                <p class="profile-section-caption">هذه البيانات خاصة بحسابك. الحقول المعلّمة بنجمة مطلوبة.</p>
                <form action="{{ route('profile.update') }}" method="post">
                    @csrf
                    @method('PATCH')
                    <section class="form-section">
                        <div class="form-grid">
                            <x-input name="name" label="الاسم الكامل" :value="$user->name" autocomplete="name" maxlength="150" required />
                            <x-input name="email" label="البريد الإلكتروني" type="email" :value="$user->email" autocomplete="email" maxlength="255" dir="ltr" required />
                        </div>
                    </section>
                    @if($studentProfile)
                        <section class="form-section">
                            <h3 class="section-title">بيانات الطالب</h3>
                            <div class="form-grid">
                                <x-input name="phone_number" label="رقم الهاتف" :value="$studentProfile->phone_number" type="tel" autocomplete="tel" dir="ltr" maxlength="30" required />
                                <x-input name="date_of_birth" label="تاريخ الميلاد" type="date" :value="$studentProfile->date_of_birth?->format('Y-m-d')" />
                                <x-input name="education_level" label="المستوى التعليمي" :value="$studentProfile->education_level" maxlength="100" />
                                <x-input name="institution_name" label="المؤسسة التعليمية" :value="$studentProfile->institution_name" maxlength="150" />
                                <x-input name="city" label="المدينة" :value="$studentProfile->city" autocomplete="address-level2" maxlength="100" />
                            </div>
                        </section>
                    @endif
                    <div class="form-actions"><x-submit label="حفظ التغييرات" icon="check" /></div>
                </form>
            </div>
        </section>

        <section class="panel" id="account-password" aria-labelledby="account-password-title">
            <h2 class="panel-heading" id="account-password-title"><x-icon name="lock" :size="19" />تغيير كلمة المرور</h2>
            <div class="panel-body">
                <p class="profile-section-caption">أدخل كلمة مرورك الحالية لتأكيد هويتك، ثم اختر كلمة مرور جديدة.</p>
                <form action="{{ route('profile.password') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <x-input name="current_password" label="كلمة المرور الحالية" type="password" autocomplete="current-password" required />
                        <x-input name="password" label="كلمة المرور الجديدة" type="password" autocomplete="new-password" minlength="12" required help="12 حرفًا على الأقل." />
                        <x-input name="password_confirmation" label="تأكيد كلمة المرور الجديدة" type="password" autocomplete="new-password" minlength="12" required />
                    </div>
                    <div class="form-actions"><x-submit label="تحديث كلمة المرور" icon="check" /></div>
                </form>
                <p class="profile-security-note"><x-icon name="shield" :size="16" /><span>لا تشارك كلمة مرورك مع الآخرين. لا تُعرض كلمات المرور المحفوظة داخل النماذج.</span></p>
            </div>
        </section>
    </div>
</x-layout>
