<x-error-shell code="403" title="الوصول غير مسموح" description="لا يملك حسابك الصلاحية اللازمة لعرض هذه الصفحة. يمكنك العودة إلى الموقع العام واستخدام الصفحات المتاحة لك." icon="lock">
    <a class="button" href="{{ route('home') }}"><x-icon name="home" :size="18" />العودة إلى الرئيسية</a>
</x-error-shell>
