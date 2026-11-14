@if($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="ترقيم الصفحات">
        <p class="pagination-summary">عرض <strong>{{ $paginator->firstItem() }}</strong> إلى <strong>{{ $paginator->lastItem() }}</strong> من أصل <strong>{{ $paginator->total() }}</strong> نتيجة</p>
        <div class="pagination-controls">
            @if($paginator->onFirstPage())
                <span class="pagination-button is-disabled" role="link" aria-disabled="true" aria-label="الصفحة السابقة"><x-icon name="chevron-right" :size="16" /><span class="pagination-word">السابق</span></span>
            @else
                <a class="pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="الصفحة السابقة"><x-icon name="chevron-right" :size="16" /><span class="pagination-word">السابق</span></a>
            @endif
            <div class="pagination-pages">
                @foreach($elements as $element)
                    @if(is_string($element))
                        <span class="pagination-ellipsis" aria-hidden="true">…</span>
                    @endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $paginator->currentPage())
                                <span class="pagination-page is-current" aria-current="page" aria-label="الصفحة {{ $page }}، الحالية">{{ $page }}</span>
                            @else
                                <a class="pagination-page" href="{{ $url }}" aria-label="الانتقال إلى الصفحة {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
            <span class="pagination-mobile-position" aria-live="polite">الصفحة <strong>{{ $paginator->currentPage() }}</strong> من {{ $paginator->lastPage() }}</span>
            @if($paginator->hasMorePages())
                <a class="pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="الصفحة التالية"><span class="pagination-word">التالي</span><x-icon name="chevron-left" :size="16" /></a>
            @else
                <span class="pagination-button is-disabled" role="link" aria-disabled="true" aria-label="الصفحة التالية"><span class="pagination-word">التالي</span><x-icon name="chevron-left" :size="16" /></span>
            @endif
        </div>
    </nav>
@endif
