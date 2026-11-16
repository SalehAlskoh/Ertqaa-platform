@props(['announcements', 'routeName' => 'announcements.show', 'targeted' => false])
<div {{ $attributes->class(['announcement-card-list']) }}>
    @forelse($announcements as $announcement)
        <article class="panel announcement-card">
            @if(!$targeted && $announcement->image_url)
                <a class="announcement-card-image" href="{{ route($routeName, $announcement) }}" tabindex="-1" aria-hidden="true"><img src="{{ $announcement->image_url }}" alt="" loading="lazy"></a>
            @endif
            <div class="announcement-card-copy">
                <div class="announcement-card-meta"><span class="announcement-category"><x-icon :name="$targeted ? 'users' : 'bell'" :size="15" />{{ $targeted ? 'إعلان للطلاب' : 'إعلان المركز' }}</span><x-date :value="$announcement->published_at" date-only /></div>
                <h2><a href="{{ route($routeName, $announcement) }}">{{ $announcement->title }}</a></h2>
                @if($targeted)<x-announcement-audience :announcement="$announcement" />@endif
                <p class="announcement-excerpt">{{ \Illuminate\Support\Str::limit($announcement->body, 190) }}</p>
                <div class="announcement-card-footer"><span>{{ $announcement->author?->name }}</span><a class="text-link" href="{{ route($routeName, $announcement) }}">قراءة الإعلان<x-icon name="arrow-left" :size="15" /></a></div>
            </div>
        </article>
    @empty
        <div class="panel announcement-empty"><x-empty :title="$targeted ? 'لا توجد إعلانات متاحة لك حاليًا' : 'لا توجد إعلانات منشورة'" :description="$targeted ? 'تظهر الإعلانات المنشورة المرتبطة بنطاقك الحالي فقط. الإشعار السابق لا يمنح وصولًا دائمًا إلى المحتوى.' : 'تابع هذه الصفحة للاطلاع على أخبار المركز عند نشرها.'" /></div>
    @endforelse
</div>
