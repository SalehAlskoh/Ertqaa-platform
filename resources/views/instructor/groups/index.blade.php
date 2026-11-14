<x-layout title="مجموعاتي التدريبية">
    <section class="panel public-hero" aria-labelledby="my-groups-title">
        <div class="public-hero-copy">
            <p class="eyebrow">مساحة المحاضر</p>
            <h1 id="my-groups-title">مجموعاتي التدريبية</h1>
            <p>استعرض المجموعات المسندة إليك، واطّلع على تفاصيل الكورس والسعة وفترة التسجيل لكل مجموعة.</p>
            <div class="public-hero-links"><a class="text-link text-xs" href="{{ route('instructor.profile') }}">ملفي المهني <x-icon name="arrow-left" :size="16" /></a></div>
        </div>
        <div class="public-hero-side"><x-icon name="users" :size="27" /><strong>{{ $groups->total() }}</strong><span>مجموعة مسندة إليك</span></div>
    </section>
    <div class="instructor-workspace-note"><x-icon name="info" :size="19" /><p>تعرض هذه المساحة مجموعاتك فقط. تعديل بيانات المجموعة وإسناد المحاضرين يتم من خلال إدارة المركز.</p></div>
    <section class="public-group-list" aria-label="مجموعاتي">
        @forelse($groups as $group)
            <x-group-row :group="$group" route-name="instructor.groups.show" />
        @empty
            <div class="panel"><x-empty title="لم تُسنَد إليك مجموعات بعد" description="ستظهر هنا مجموعاتك التدريبية فور إسنادها من إدارة المركز." /></div>
        @endforelse
    </section>
    @if($groups->hasPages())
        <div class="panel mt-5"><div class="pagination">{{ $groups->links() }}</div></div>
    @endif
</x-layout>
