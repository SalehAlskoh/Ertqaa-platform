@php
    $availableSeats = max(0, $trainingGroup->capacity - $occupiedSeats - $reservedSeats);
    $notOpenYet = $trainingGroup->registration_opens_at && now()->lt($trainingGroup->registration_opens_at);
    $registrationEnded = !$trainingGroup->registration_closes_at || now()->gte($trainingGroup->registration_closes_at);
@endphp
<x-layout :title="$trainingGroup->course?->title ?? 'تفاصيل المجموعة'">
    <x-breadcrumbs :items="[['label'=>'المجموعات التدريبية','url'=>route('groups.index')],['label'=>$trainingGroup->code]]" />
    <section class="panel group-hero" aria-labelledby="public-group-title">
        <div class="group-hero-header">
            <div>
                <div class="group-hero-meta"><x-badge :status="$trainingGroup->status" /><span class="group-code">رمز المجموعة: <bdi>{{ $trainingGroup->code }}</bdi></span></div>
                <h1 class="page-title" id="public-group-title">{{ $trainingGroup->course?->title ?? 'المجموعة التدريبية' }}</h1>
                <p class="page-description">معلومات المجموعة ومواعيد التسجيل المعلنة بتوقيت طرابلس.</p>
            </div>
            <div class="group-hero-actions"><a class="button button-secondary" href="{{ route('courses.show', $trainingGroup->course->slug) }}"><x-icon name="book" :size="18" />تفاصيل الكورس</a></div>
        </div>
    </section>
    <section class="panel apply-panel" aria-labelledby="apply-title">
        <div>
            <h2 id="apply-title">طلب الالتحاق بالمجموعة</h2>
            <p>أرسل طلبك للمراجعة. يبدأ حجز المقعد بعد القبول المبدئي، ويُستكمل التسجيل حضوريًا لدى موظف المركز خلال المهلة المحددة.</p>
            <div class="apply-panel-actions">
                @if($application)
                    <x-application-badge :application="$application" />
                    <a class="button" href="{{ route('student.applications.show', $application) }}">متابعة طلبي<x-icon name="chevron-left" :size="16" /></a>
                    <p class="field-help">لديك طلب سابق لهذه المجموعة. لا يمكن تقديم طلب آخر.</p>
                @elseif($canApply)
                    <form action="{{ route('student.applications.store', $trainingGroup) }}" method="post">
                        @csrf
                        <x-submit label="تقديم طلب الانضمام" icon="file" />
                    </form>
                    <span class="field-help">تقديم الطلب لا يضمن المقعد ولا يُنشئ تسجيلًا نهائيًا.</span>
                @elseif($notOpenYet)
                    <p class="field-help">لم تبدأ فترة استقبال الطلبات بعد.</p>
                @elseif($registrationEnded)
                    <p class="field-help">انتهت فترة استقبال الطلبات لهذه المجموعة.</p>
                @elseif($availableSeats === 0)
                    <p class="field-help">لا توجد مقاعد متاحة حاليًا بعد احتساب التسجيلات والحجوزات السارية.</p>
                @else
                    @guest
                        <a class="button" href="{{ route('login') }}">تسجيل الدخول للتقديم<x-icon name="arrow-left" :size="16" /></a>
                        <a class="button button-secondary" href="{{ route('register') }}">إنشاء حساب طالب</a>
                    @else
                        @if(auth()->user()->hasRole('student'))
                            <p class="field-help">يتطلب التقديم حساب طالب مؤهلًا وملف طالب مكتملًا. راجع بيانات حسابك.</p><a class="button button-secondary" href="{{ route('profile.edit') }}">حسابي</a>
                        @else
                            <p class="field-help">التقديم متاح لحسابات الطلاب فقط.</p>
                        @endif
                    @endguest
                @endif
            </div>
        </div>
        <div><x-capacity-summary :capacity="$trainingGroup->capacity" :occupied="$occupiedSeats" :reserved="$reservedSeats" /><p class="field-help mt-4">المقاعد المتاحة الآن: {{ $availableSeats }}. تُراجع السعة عند معالجة كل طلب.</p></div>
    </section>
    <x-group-details :training-group="$trainingGroup" />
</x-layout>
