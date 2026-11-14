@php($group = $enrollment->application->trainingGroup)
<x-layout title="تفاصيل تسجيلي">
    <x-breadcrumbs :items="[['label'=>'تسجيلاتي','url'=>route('student.enrollments.index')],['label'=>'تسجيل #'.$enrollment->id]]" />
    <x-page-heading :title="$group->course?->title ?? 'تفاصيل التسجيل'" :description="'تسجيل #'.$enrollment->id.' · المجموعة '.$group->code"><x-badge :status="$enrollment->status" :label="$enrollment->status === 'active' ? 'تسجيل نشط' : 'منسحب'" /></x-page-heading>
    <div class="workflow-grid">
        <div class="workflow-main">
            <section class="panel"><h2 class="panel-heading"><x-icon name="file" :size="19" />سجل التسجيل والتحصيل</h2><div class="panel-body"><x-enrollment-record :enrollment="$enrollment" :currency="$group->currency_code" show-actor /><p class="field-help mt-6">تُعرض بيانات التحصيل التي حفظها موظف المركز وقت التأكيد. هذه الصفحة للعرض ولا تُنفذ مدفوعات أو استردادًا.</p></div></section>
            <section class="panel"><h2 class="panel-heading"><x-icon name="book" :size="19" />المجموعة والطلب</h2><div class="panel-body"><dl class="detail-list"><div><dt>الكورس</dt><dd>{{ $group->course?->title }}</dd></div><div><dt>رمز المجموعة</dt><dd><bdi>{{ $group->code }}</bdi></dd></div><div><dt>الطالب</dt><dd>{{ $enrollment->application->student?->name }}</dd></div><div><dt>طلب التسجيل</dt><dd><a class="text-link" href="{{ route('student.applications.show', $enrollment->application) }}">طلب #{{ $enrollment->application->id }}</a></dd></div></dl></div></section>
        </div>
        <aside class="workflow-side">
            @if($enrollment->status === 'active')
                <section class="panel"><h2 class="panel-heading">متابعة التعلم</h2><div class="panel-body"><a class="button" href="{{ route('student.lectures.index', ['group'=>$group->id]) }}"><x-icon name="calendar" :size="17" />محاضرات المجموعة</a></div></section>
                @can('withdraw', $enrollment)
                    <section class="panel"><h2 class="panel-heading">الانسحاب من المجموعة</h2><div class="panel-body form-stack"><p class="field-help">الانسحاب ينهي التسجيل النشط ويحرر المقعد. لا يعني استرداد الرسوم، وتبقى بيانات التحصيل محفوظة. لا توجد إعادة تفعيل تلقائية.</p>
                        <form action="{{ route('student.enrollments.withdraw', $enrollment) }}" method="post" data-confirm="هل تريد الانسحاب من المجموعة؟ سيُحرر مقعدك وتفقد الوصول إلى محاضراتها. لا تُسترد الرسوم بهذا الإجراء، ويبقى السجل المالي محفوظًا.">
                            @csrf
                            @method('PATCH')
                            <button class="button button-danger" type="submit">الانسحاب من المجموعة</button>
                        </form>
                    </div></section>
                @endcan
            @else
                <div class="notice notice-info"><x-icon name="info" /><p>أُغلق هذا التسجيل بالانسحاب. تبقى بياناته محفوظة للرجوع إليها دون استرداد تلقائي للرسوم أو إعادة تفعيل.</p></div>
            @endif
        </aside>
    </div>
</x-layout>
