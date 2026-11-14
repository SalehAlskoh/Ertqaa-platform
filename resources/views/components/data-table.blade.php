@props(['title', 'count' => null, 'label' => null])
<section {{ $attributes->class(['panel', 'table-panel']) }}>
    <div class="table-panel-heading"><h2>{{ $title }}@if($count !== null)<span class="result-count">{{ $count }}</span>@endif</h2><span class="table-scroll-hint"><x-icon name="arrow-left" :size="14" />اسحب أفقيًا لعرض بقية الأعمدة</span></div>
    <div class="table-scroll" tabindex="0" role="region" aria-label="{{ $label ?? $title.'، قابل للتمرير أفقيًا' }}">
        <table class="data-table"><caption class="visually-hidden">{{ $label ?? $title }}</caption><thead>{{ $head }}</thead><tbody>{{ $slot }}</tbody></table>
    </div>
    @isset($pagination)
        <div class="pagination">{{ $pagination }}</div>
    @endisset
</section>
