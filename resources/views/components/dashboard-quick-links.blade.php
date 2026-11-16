@props(['links', 'title' => 'وصول سريع'])
<nav class="panel dashboard-quick-links" aria-label="{{ $title }}">
    <span class="dashboard-quick-label"><x-icon name="grid" :size="17" />{{ $title }}</span>
    <div>
        @foreach($links as $link)
            <a class="button button-secondary button-small" href="{{ $link['url'] }}"><x-icon :name="$link['icon'] ?? 'arrow-left'" :size="16" />{{ $link['label'] }}</a>
        @endforeach
    </div>
</nav>
