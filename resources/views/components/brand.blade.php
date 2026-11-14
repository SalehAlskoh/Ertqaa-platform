@props(['href' => null])
<a class="brand" href="{{ $href ?? route('home') }}" aria-label="منصة ارتقاء — الصفحة الرئيسية">
    <span class="brand-emblem"><x-icon name="book" :size="25" /></span>
    <span class="brand-copy"><span class="brand-name">منصة ارتقاء<span class="brand-dot" aria-hidden="true"></span></span><span class="brand-subtitle">إدارة المركز التدريبي</span></span>
</a>
