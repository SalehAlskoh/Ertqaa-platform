@props(['announcement', 'targeted' => false, 'management' => false])
<article class="panel announcement-article">
    <header class="announcement-article-header">
        <div class="announcement-article-label"><span class="announcement-category"><x-icon :name="$targeted ? 'users' : 'bell'" :size="17" />{{ $targeted ? 'إعلان موجّه للطلاب' : 'من إعلانات المركز' }}</span>@if($management)<x-announcement-status :announcement="$announcement" />@endif</div>
        <h1>{{ $announcement->title }}</h1>
        <div class="announcement-article-meta"><span><x-icon name="user" :size="16" />{{ $announcement->author?->name }}</span><span><x-icon name="calendar" :size="16" />@if($announcement->published_at)<x-date :value="$announcement->published_at" />@else لم يُنشر بعد @endif</span></div>
        @if($targeted)<div class="announcement-target-note"><x-announcement-audience :announcement="$announcement" /></div>@endif
    </header>
    @if(!$targeted && $announcement->image_url)<img class="announcement-article-image" src="{{ $announcement->image_url }}" alt="صورة مرفقة بإعلان {{ $announcement->title }}">@endif
    <div class="announcement-article-body">{{ $announcement->body }}</div>
</article>
