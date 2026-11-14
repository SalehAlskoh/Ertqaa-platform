@props(['title', 'description' => null, 'icon' => null])
<section {{ $attributes->class(['form-section']) }}>
    <h2 class="form-section-title">@if($icon)<x-icon :name="$icon" :size="19" />@endif{{ $title }}</h2>
    @if($description)<p class="form-section-description">{{ $description }}</p>@endif
    {{ $slot }}
</section>
