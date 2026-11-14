@props(['items'])
<nav class="breadcrumb" aria-label="مسار الصفحة">
    @foreach($items as $item)
        @unless($loop->first)
            <x-icon name="chevron-left" :size="13" />
        @endunless
        @if(isset($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
            <span @if($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
