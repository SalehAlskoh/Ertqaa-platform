@props(['label' => 'حفظ البيانات'])
<button {{ $attributes->class(['button']) }} type="submit"><span class="submit-label">{{ $label }}</span><span class="loading-indicator" role="status">جارٍ الحفظ…</span></button>
