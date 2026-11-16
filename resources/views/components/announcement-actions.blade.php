@props(['announcement', 'kind' => 'public', 'showDetails' => true, 'showEdit' => true])
@php($prefix = $kind === 'public' ? 'admin.announcements.' : 'admin.student-announcements.')
<div {{ $attributes->class(['row-actions','announcement-actions']) }}>
    @if($showDetails)
        <a class="button button-secondary button-small" href="{{ route($prefix.'show', $announcement) }}">التفاصيل</a>
    @endif
    @if($showEdit)
        @can('update', $announcement)
            <a class="button button-secondary button-small" href="{{ route($prefix.'edit', $announcement) }}"><x-icon name="edit" :size="15" />تعديل</a>
        @endcan
    @endif
    @if($announcement->publication_status !== 'published')
        @can('publish', $announcement)
            <form action="{{ route($prefix.'publish', $announcement) }}" method="post" data-confirm="{{ $kind === 'public' ? 'هل تريد نشر الإعلان ليصبح متاحًا لزوار الموقع؟' : 'هل تريد نشر الإعلان للطلاب المستحقين وإرسال إشعار داخلي عام لهم؟' }}">
                @csrf
                @method('PATCH')
                <button class="button button-small" type="submit"><x-icon name="eye" :size="15" />نشر الآن</button>
            </form>
        @endcan
    @endif
    @if($announcement->published_at)
        @can('unpublish', $announcement)
            <form action="{{ route($prefix.'unpublish', $announcement) }}" method="post" data-confirm="هل تريد إلغاء نشر الإعلان؟ سيختفي من صفحات القراءة مع الاحتفاظ بسجله.">
                @csrf
                @method('PATCH')
                <button class="button button-secondary button-small" type="submit">إلغاء النشر</button>
            </form>
        @endcan
    @endif
</div>
