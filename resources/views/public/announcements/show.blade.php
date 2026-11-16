<x-layout :title="$announcement->title">
    <x-breadcrumbs :items="[['label'=>'إعلانات المركز','url'=>route('announcements.index')],['label'=>'تفاصيل الإعلان']]" />
    <div class="announcement-reading-layout"><x-announcement-content :announcement="$announcement" /><aside class="panel announcement-reading-aside"><x-icon name="bell" :size="24" /><h2>ابقَ على اطلاع</h2><p>المستجدات الرسمية للمركز في مكان واحد. يمكنك الرجوع إلى قائمة الإعلانات المنشورة أو استكشاف الكورسات.</p><a class="button button-secondary" href="{{ route('announcements.index') }}">جميع الإعلانات</a><a class="text-link" href="{{ route('courses.index') }}">استكشف الكورسات<x-icon name="arrow-left" :size="15" /></a></aside></div>
</x-layout>
