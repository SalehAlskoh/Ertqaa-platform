@props(['title' => 'لا توجد نتائج', 'description' => 'جرّب تغيير كلمات البحث أو خيارات التصفية.'])
<div class="empty-state"><div class="empty-symbol" aria-hidden="true">≡</div><h2 class="empty-title">{{ $title }}</h2><p class="empty-description">{{ $description }}</p>{{ $slot }}</div>
