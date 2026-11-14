<x-error-shell code="404" title="لم نعثر على الصفحة" description="قد يكون الرابط غير صحيح أو المحتوى غير متاح للعرض حاليًا. يمكنك استكشاف الكورسات المنشورة من دليل ارتقاء." icon="search">
    <a class="button" href="{{ route('courses.index') }}"><x-icon name="book" :size="18" />استكشاف الكورسات</a>
    <a class="button button-secondary" href="{{ route('home') }}">الرئيسية</a>
</x-error-shell>
