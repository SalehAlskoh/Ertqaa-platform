<x-layout title="تسجيل الدخول">
    <div class="auth-shell auth-shell-redesigned">
        <aside class="auth-aside">
            <div>
                <span class="auth-intro-mark"><x-icon name="book" :size="27" /></span>
                <p class="eyebrow">مرحبًا بعودتك إلى ارتقاء</p>
                <h2>خطوتك التالية<br>تبدأ من هنا.</h2>
                <p>ادخل إلى مساحتك لمتابعة كورسات المركز ومجموعاته وإدارة حسابك.</p>
            </div>
            <div class="auth-aside-caption">
                <x-icon name="shield" :size="21" />
                <p>مساحة واحدة تجمع حسابك<br>وتمنح طموحك مجالًا للنمو.</p>
            </div>
        </aside>
        <section class="auth-content" aria-labelledby="login-title">
            <div class="auth-form-heading">
                <p class="eyebrow">مساحتك في ارتقاء</p>
                <h1 class="auth-title" id="login-title">تسجيل الدخول</h1>
                <p class="auth-subtitle">أدخل البريد الإلكتروني وكلمة مرور حسابك.</p>
            </div>
            <form class="auth-form" action="{{ route('login.store') }}" method="post">
                @csrf
                <x-input name="email" label="البريد الإلكتروني" type="email" icon="mail" autocomplete="username" required dir="ltr" maxlength="255" placeholder="name@example.com" />
                <x-input name="password" label="كلمة المرور" type="password" icon="lock" autocomplete="current-password" required />
                <div class="auth-remember"><label class="checkbox-label"><input type="checkbox" name="remember" value="1" @checked(old('remember'))>تذكّر دخولي على هذا الجهاز</label></div>
                <x-submit label="تسجيل الدخول" busy-label="جارٍ تسجيل الدخول…" icon="arrow-left" />
            </form>
            <p class="auth-footer">ليس لديك حساب؟ <a class="text-link" href="{{ route('register') }}">إنشاء حساب طالب</a></p>
            <a class="auth-site-return" href="{{ route('courses.index') }}"><x-icon name="arrow-right" :size="15" />العودة لاستكشاف الكورسات</a>
        </section>
    </div>
</x-layout>
