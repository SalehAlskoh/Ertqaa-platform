<x-error-shell code="419" title="انتهت صلاحية الصفحة" description="لحماية جلستك، يرجى فتح الصفحة مجددًا قبل إرسال النموذج. يمكنك تسجيل الدخول من جديد أو العودة إلى الموقع العام." icon="shield">
    <a class="button" href="{{ route('login') }}"><x-icon name="user" :size="18" />تسجيل الدخول</a>
    <a class="button button-secondary" href="{{ route('home') }}">العودة إلى الرئيسية</a>
</x-error-shell>
