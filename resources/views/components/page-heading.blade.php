@props(['title', 'eyebrow' => null, 'description' => null])
<div class="page-heading"><div>@if($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif<h1 class="page-title">{{ $title }}</h1>@if($description)<p class="page-description">{{ $description }}</p>@endif</div>@if(trim($slot))<div class="heading-actions">{{ $slot }}</div>@endif</div>
