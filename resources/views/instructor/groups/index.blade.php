<x-layout title="مجموعاتي التدريبية">
    <x-page-heading title="مجموعاتي التدريبية" eyebrow="مساحة المحاضر" description="استعرض المجموعات المسندة إليك واطّلع على تفاصيل كل مجموعة." />
    <section class="catalog-list">@forelse($groups as $group)<x-group-row :group="$group" route-name="instructor.groups.show" />@empty<x-empty title="لم تُسنَد إليك مجموعات بعد" description="ستظهر هنا مجموعاتك التدريبية فور إسنادها من إدارة المركز." />@endforelse @if($groups->hasPages())<div class="pagination">{{ $groups->links() }}</div>@endif</section>
</x-layout>
