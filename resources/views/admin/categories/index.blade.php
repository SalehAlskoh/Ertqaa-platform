<x-layout title="إدارة التصنيفات">
    <x-page-heading title="تصنيفات الكورسات" description="تنظيم الكورسات في تصنيفات واضحة يسهل على الطلاب استكشافها."><a class="button" href="{{ route('admin.course-categories.create') }}"><x-icon name="plus" :size="18" />إضافة تصنيف</a></x-page-heading>
    @if($categories->count())
        <x-data-table title="التصنيفات" :count="$categories->total()">
            <x-slot:head><tr><th scope="col">التصنيف</th><th scope="col">الرابط المختصر</th><th scope="col">الكورسات المرتبطة</th><th scope="col">الإجراءات</th></tr></x-slot:head>
            @foreach($categories as $category)
                <tr>
                    <td><span class="table-meta"><x-icon name="tag" :size="18" /><strong class="table-primary">{{ $category->name }}</strong></span></td>
                    <td><bdi class="table-secondary">{{ $category->slug }}</bdi></td>
                    <td><span class="linked-count"><x-icon name="book" :size="15" />{{ $category->courses_count }} كورس</span></td>
                    <td><div class="row-actions">
                        <a class="button button-secondary button-small" href="{{ route('admin.course-categories.edit', $category) }}"><x-icon name="edit" :size="15" />تعديل</a>
                        @if((int)$category->courses_count === 0)
                            <form action="{{ route('admin.course-categories.destroy', $category) }}" method="post" data-confirm="هل تريد حذف هذا التصنيف غير المرتبط بكورسات؟">
                                @csrf
                                @method('DELETE')
                                <button class="icon-button" type="submit" aria-label="حذف تصنيف {{ $category->name }}" title="حذف التصنيف"><x-icon name="trash" :size="17" /></button>
                            </form>
                        @else
                            <span class="field-help">مرتبط بكورسات</span>
                        @endif
                    </div></td>
                </tr>
            @endforeach
            @if($categories->hasPages())
                <x-slot:pagination>{{ $categories->withQueryString()->links() }}</x-slot:pagination>
            @endif
        </x-data-table>
    @else
        <section class="panel"><x-empty title="ابدأ بتنظيم الكورسات" description="أضف أول تصنيف لتتمكن من إنشاء الكورسات التابعة له."><a class="button button-secondary" href="{{ route('admin.course-categories.create') }}"><x-icon name="plus" :size="17" />إضافة تصنيف</a></x-empty></section>
    @endif
</x-layout>
