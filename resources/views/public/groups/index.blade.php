<x-layout title="المجموعات التدريبية">
    <section class="panel public-hero" aria-labelledby="groups-title">
        <div class="public-hero-copy">
            <p class="eyebrow">مسارات التعلّم</p>
            <h1 id="groups-title">المجموعات التدريبية</h1>
            <p>اطّلع على الكورسات ومحاضريها والسعة والرسوم وفترات التسجيل المعلنة للمجموعات المنشورة.</p>
            <div class="public-hero-links"><a class="text-link text-xs" href="{{ route('courses.index') }}">استكشف دليل الكورسات <x-icon name="arrow-left" :size="16" /></a></div>
        </div>
        <div class="public-hero-side"><x-icon name="users" :size="27" /><strong>{{ $groups->total() }}</strong><span>مجموعة منشورة تطابق التصفية</span></div>
    </section>

    <section class="filter-panel" aria-label="البحث وتصفية المجموعات">
        <form class="filters" action="{{ route('groups.index') }}" method="get">
            <div class="field search-field">
                <label class="field-label" for="q">البحث</label>
                <div class="input-with-icon"><x-icon name="search" :size="19" /><input class="input" id="q" name="q" value="{{ request('q') }}" placeholder="عنوان الكورس أو رمز المجموعة…" maxlength="200"></div>
            </div>
            <div class="field">
                <label class="field-label" for="course">الكورس</label>
                <select class="input" id="course" name="course">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string)request('course') === (string)$course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="button"><x-icon name="search" :size="18" />بحث</button>
                @if(request()->filled('q') || request()->filled('course'))
                    <a class="button button-secondary" href="{{ route('groups.index') }}"><x-icon name="refresh" :size="16" />مسح التصفية</a>
                @endif
            </div>
        </form>
    </section>

    <div class="public-results-bar"><h2>المجموعات المنشورة <span class="result-count">{{ $groups->total() }}</span></h2><p>التسجيل النهائي واستيفاء الرسوم حضوريًا</p></div>
    <section class="public-group-list" aria-label="نتائج المجموعات">
        @forelse($groups as $group)
            <x-group-row :group="$group" />
        @empty
            <div class="panel"><x-empty title="لا توجد مجموعات منشورة مطابقة" description="جرّب البحث بعنوان أو رمز مختلف، أو تغيير الكورس المختار." /></div>
        @endforelse
    </section>
    @if($groups->hasPages())
        <div class="panel mt-5"><div class="pagination">{{ $groups->withQueryString()->links() }}</div></div>
    @endif
    <p class="public-inline-note mt-6"><x-icon name="info" :size="17" /><span>استعرض تفاصيل المجموعة للتقديم خلال فترة التسجيل. حجز المقعد يبدأ بعد القبول المبدئي.</span></p>
</x-layout>
