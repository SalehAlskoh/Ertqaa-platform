<x-layout title="إعلانات المركز">
    <section class="panel announcement-public-heading">
        <div><p class="eyebrow">أخبار المركز ومستجداته</p><h1>إعلانات المركز</h1><p>تابع الإعلانات المنشورة، ومواعيد فتح التسجيل، وآخر أخبار ارتقاء.</p></div><span class="announcement-heading-icon" aria-hidden="true"><x-icon name="bell" :size="32" /></span>
    </section>
    <section class="filter-panel" aria-label="البحث في إعلانات المركز"><form class="filters" action="{{ route('announcements.index') }}" method="get"><div class="field search-field"><label class="field-label" for="q">البحث في الإعلانات</label><div class="input-with-icon"><x-icon name="search" :size="18" /><input class="input" name="q" id="q" value="{{ request('q') }}" maxlength="200" placeholder="عنوان الإعلان أو محتواه…"></div></div><div class="filter-actions"><button class="button" type="submit"><x-icon name="search" :size="17" />بحث</button>@if(request()->filled('q'))<a class="button button-secondary" href="{{ route('announcements.index') }}">مسح البحث</a>@endif</div></form></section>
    <div class="public-results-bar"><h2>الإعلانات المنشورة <span class="result-count">{{ $announcements->total() }}</span></h2><p>النتائج المطابقة للبحث</p></div>
    <x-announcement-cards :announcements="$announcements" />
    @if($announcements->hasPages())<div class="panel mt-5"><div class="pagination">{{ $announcements->links() }}</div></div>@endif
</x-layout>
