@props(['title', 'icon' => null, 'description' => null, 'url' => null, 'linkLabel' => 'عرض الكل', 'tone' => 'neutral'])
<section {{ $attributes->class(['panel', 'dashboard-panel', 'dashboard-panel-'.$tone]) }} aria-label="{{ $title }}">
    <header class="dashboard-panel-heading">
        <div><h2>@if($icon)<x-icon :name="$icon" :size="19" />@endif{{ $title }}</h2>
            @if($description)<p>{{ $description }}</p>@endif
        </div>
        @if($url)<a class="text-link dashboard-view-all" href="{{ $url }}">{{ $linkLabel }}<x-icon name="arrow-left" :size="15" /></a>@endif
    </header>
    {{ $slot }}
</section>
