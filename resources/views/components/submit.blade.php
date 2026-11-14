@props(['label' => 'حفظ البيانات', 'busyLabel' => 'جارٍ الحفظ…', 'icon' => null])
<button {{ $attributes->class(['button']) }} type="submit">
    <span class="submit-label">@if($icon)<x-icon :name="$icon" :size="18" />@endif{{ $label }}</span>
    <span class="loading-indicator" role="status">{{ $busyLabel }}</span>
</button>
