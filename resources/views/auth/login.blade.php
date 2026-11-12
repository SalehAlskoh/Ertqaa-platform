<x-layout title="تسجيل الدخول">
    <div class="auth-shell">
        <aside class="auth-aside"><div><p class="eyebrow">مرحبًا بعودتك</p><h2>خطوتك التالية<br>تبدأ من هنا.</h2><p>ادخل إلى حسابك لمتابعة مسيرتك مع ارتقاء.</p></div><div class="auth-aside-bottom"><div class="h-px w-16 bg-stone-400/50 mb-5"></div><p>معرفة أعمق. مهارات أقوى.<br>ومساحة أكبر للطموح.</p></div></aside>
        <section class="auth-content"><h1 class="auth-title">تسجيل الدخول</h1><p class="auth-subtitle">أدخل بيانات حسابك للمتابعة.</p><form action="{{ route('login.store') }}" method="post">@csrf<x-input name="email" label="البريد الإلكتروني" type="email" autocomplete="username" required dir="ltr" maxlength="255" /><x-input name="password" label="كلمة المرور" type="password" autocomplete="current-password" required /><div class="my-5"><label class="checkbox-label"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> تذكّر دخولي على هذا الجهاز</label></div><x-submit label="تسجيل الدخول" class="w-full" /></form><p class="auth-footer">ليس لديك حساب؟ <a class="text-link" href="{{ route('register') }}">إنشاء حساب طالب</a></p></section>
    </div>
</x-layout>
