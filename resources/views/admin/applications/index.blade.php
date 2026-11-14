<x-layout title="طلبات التسجيل">
    <x-page-heading title="مراجعة طلبات التسجيل" description="راجع الطلبات، امنح القبول المبدئي، ثم أكد التسجيل بعد الحضور واستيفاء الرسوم." />
    <div class="notice notice-info"><x-icon name="clock" /><p>القبول المبدئي يحجز مقعدًا لمدة 48 ساعة من الموافقة الأولى. لا يكتمل التسجيل إلا بتأكيد موظف مخوّل خلال المهلة، ولا تُمدد المهلة بإعادة المحاولة.</p></div>
    <section class="filter-panel" aria-label="تصفية طلبات التسجيل">
        <form class="filters" action="{{ route('admin.applications.index') }}" method="get">
            <div class="field search-field"><label class="field-label" for="q">البحث عن طالب</label><div class="input-with-icon"><x-icon name="search" :size="18" /><input class="input" id="q" name="q" maxlength="200" value="{{ request('q') }}" placeholder="اسم الطالب أو بريده الإلكتروني…"></div></div>
            <x-select name="group" label="المجموعة"><option value="">جميع المجموعات</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected((string)request('group') === (string)$group->id)>{{ $group->code }} · {{ $group->course?->title }}</option>
                @endforeach
            </x-select>
            <x-select name="status" label="حالة الطلب"><option value="">جميع الحالات</option>
                @foreach(['pending'=>'قيد المراجعة','awaiting_confirmation'=>'بانتظار التأكيد الحضوري','rejected'=>'مرفوض','expired'=>'منتهي المهلة','cancelled'=>'ملغى','completed'=>'مكتمل'] as $value=>$label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </x-select>
            <div class="filter-actions"><button class="button" type="submit">تطبيق</button>
                @if(request()->hasAny(['q','group','status']))
                    <a class="button button-secondary" href="{{ route('admin.applications.index') }}">مسح التصفية</a>
                @endif
            </div>
        </form>
    </section>
    @if($applications->count())
        <x-data-table title="سجل طلبات التسجيل" :count="$applications->total()">
            <x-slot:head><tr><th scope="col">الطلب والطالب</th><th scope="col">المجموعة</th><th scope="col">الحالة</th><th scope="col">مهلة الحجز</th><th scope="col">الإجراء</th></tr></x-slot:head>
            @foreach($applications as $application)
                <tr>
                    <td><strong class="table-primary">{{ $application->student?->name }}</strong><span class="table-secondary"><bdi>{{ $application->student?->email }}</bdi></span><p class="workflow-record-id">طلب #{{ $application->id }} · <x-date :value="$application->created_at" date-only /></p></td>
                    <td><strong class="table-primary">{{ $application->trainingGroup?->course?->title }}</strong><span class="table-secondary"><bdi>{{ $application->trainingGroup?->code }}</bdi></span></td>
                    <td><x-application-badge :application="$application" /></td>
                    <td><x-offer-deadline :application="$application" compact /></td>
                    <td><a class="button button-secondary button-small" href="{{ route('admin.applications.show', $application) }}">مراجعة الطلب</a></td>
                </tr>
            @endforeach
            @if($applications->hasPages())
                <x-slot:pagination>{{ $applications->withQueryString()->links() }}</x-slot:pagination>
            @endif
        </x-data-table>
    @else
        <section class="panel"><x-empty title="لا توجد طلبات مطابقة" description="ستظهر هنا الطلبات المقدّمة من الطلاب. يمكنك تعديل التصفية لعرض سجلات أخرى." /></section>
    @endif
</x-layout>
