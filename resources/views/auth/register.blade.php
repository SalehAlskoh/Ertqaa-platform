<x-layout title="إنشاء حساب طالب">
    <div class="auth-shell auth-shell-redesigned">
        <aside class="auth-aside">
            <div>
                <span class="auth-intro-mark"><x-icon name="book" :size="27" /></span>
                <p class="eyebrow">مسيرتك مع ارتقاء</p>
                <h2>ابدأ التعلّم.<br>واصنع فرصتك.</h2>
                <p>أنشئ حساب الطالب الخاص بك، واستكشف الكورسات والمجموعات التدريبية المتاحة.</p>
            </div>
            <div class="auth-aside-caption">
                <x-icon name="user" :size="21" />
                <p>التسجيل الذاتي مخصص للطلاب.<br>حسابات فريق المركز تُدار عبر الإدارة.</p>
            </div>
        </aside>
        <section class="auth-content" aria-labelledby="register-title">
            <div class="auth-form-heading">
                <p class="eyebrow">انضم إلى ارتقاء</p>
                <h1 class="auth-title" id="register-title">حساب طالب جديد</h1>
                <p class="auth-subtitle">املأ بياناتك الأساسية لإنشاء حسابك.</p>
            </div>
            <form class="auth-form" action="{{ route('register.store') }}" method="post">
                @csrf
                <x-input name="name" label="الاسم الكامل" icon="user" autocomplete="name" maxlength="150" required />
                <x-input name="email" label="البريد الإلكتروني" type="email" icon="mail" autocomplete="username" dir="ltr" maxlength="255" required placeholder="name@example.com" />
                <x-input name="phone_number" label="رقم الهاتف" type="tel" autocomplete="tel" dir="ltr" maxlength="30" required />
                <div class="auth-password-grid">
                    <x-input name="password" label="كلمة المرور" type="password" autocomplete="new-password" minlength="12" required help="12 حرفًا على الأقل." />
                    <x-input name="password_confirmation" label="تأكيد كلمة المرور" type="password" autocomplete="new-password" minlength="12" required />
                </div>
                <x-submit label="إنشاء حسابي" busy-label="جارٍ إنشاء الحساب…" icon="arrow-left" />
            </form>
            <p class="auth-footer">لديك حساب بالفعل؟ <a class="text-link" href="{{ route('login') }}">تسجيل الدخول</a></p>
            <a class="auth-site-return" href="{{ route('courses.index') }}"><x-icon name="arrow-right" :size="15" />العودة إلى دليل الكورسات</a>
        </section>
    </div>
</x-layout>
