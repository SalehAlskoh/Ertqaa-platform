@props(['label', 'value', 'icon' => 'grid', 'note' => null, 'tone' => 'neutral', 'unit' => null])
<article {{ $attributes->class(['stat-card', 'stat-card-'.$tone]) }}>
    <div class="stat-card-top"><p class="stat-label">{{ $label }}</p><span class="stat-icon"><x-icon :name="$icon" :size="22" /></span></div>
    <div class="stat-quantity"><strong>{{ $value }}</strong>@if($unit)<span>{{ $unit }}</span>@endif</div>
    @if($note)<p class="stat-note">{{ $note }}</p>@endif
</article>
