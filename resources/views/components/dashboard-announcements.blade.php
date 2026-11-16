@props(['announcements', 'title', 'url', 'showRoute', 'targeted' => false, 'management' => false])
<x-dashboard-panel :title="$title" icon="bell" description="أحدث 5 إعلانات ضمن هذا النطاق كحد أقصى." :url="$url">
    @if($announcements->isNotEmpty())
        <div class="announcement-preview-list">
            @foreach($announcements as $announcement)
                <article><div class="announcement-preview-heading"><h3><a href="{{ route($showRoute, $announcement) }}">{{ $announcement->title }}</a></h3>@if($management)<x-announcement-status :announcement="$announcement" />@endif</div>
                    @if($targeted)<x-announcement-audience :announcement="$announcement" />@endif
                    <div class="announcement-preview-meta"><x-date :value="$announcement->published_at ?? $announcement->created_at" date-only /><a class="text-link" href="{{ route($showRoute, $announcement) }}">التفاصيل<x-icon name="chevron-left" :size="13" /></a></div>
                </article>
            @endforeach
        </div>
    @else
        <x-empty title="لا توجد إعلانات في هذا النطاق" description="تظهر الإعلانات الفعلية هنا عند توفرها بحسب صلاحية حسابك." />
    @endif
</x-dashboard-panel>
