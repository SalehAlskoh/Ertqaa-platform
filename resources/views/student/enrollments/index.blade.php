<x-layout title="تسجيلاتي">
    <x-page-heading title="تسجيلاتي" description="سجل التسجيلات التي أكدها المركز، بما فيها التسجيلات المنسحب منها."><a class="button button-secondary" href="{{ route('student.lectures.index') }}"><x-icon name="calendar" :size="17" />محاضراتي</a></x-page-heading>
    @if($enrollments->count())
        <div class="application-list">
            @foreach($enrollments as $enrollment)
                @php($group = $enrollment->application?->trainingGroup)
                <article class="panel application-card">
                    <div class="application-card-header"><div><span class="workflow-record-id">تسجيل #{{ $enrollment->id }}</span><h2>{{ $group?->course?->title }}</h2><div class="application-card-meta"><span>المجموعة <bdi>{{ $group?->code }}</bdi></span><span>تاريخ التأكيد <x-date :value="$enrollment->enrolled_at" date-only /></span></div></div><x-badge :status="$enrollment->status" :label="$enrollment->status === 'active' ? 'تسجيل نشط' : 'منسحب'" /></div>
                    <div class="application-card-body"><p class="field-help">{{ $enrollment->status === 'active' ? 'تسجيلك نشط. يمكنك الاطلاع على المحاضرات الخاصة بالمجموعة.' : 'سجل الانسحاب محفوظ مع بيانات التسجيل والتحصيل السابقة.' }}</p><div class="application-card-actions"><a class="button button-secondary" href="{{ route('student.enrollments.show', $enrollment) }}">تفاصيل التسجيل</a>
                        @if($enrollment->status === 'active')
                            <a class="button" href="{{ route('student.lectures.index', ['group'=>$group?->id]) }}">محاضرات المجموعة</a>
                        @endif
                    </div></div>
                </article>
            @endforeach
        </div>
        @if($enrollments->hasPages())
            <div class="pagination">{{ $enrollments->links() }}</div>
        @endif
    @else
        <section class="panel"><x-empty title="لا توجد تسجيلات نهائية بعد" description="يظهر التسجيل هنا بعد تأكيده حضوريًا لدى موظف المركز. يمكنك متابعة طلباتك الحالية."><a class="button" href="{{ route('student.applications.index') }}">متابعة طلباتي</a></x-empty></section>
    @endif
</x-layout>
