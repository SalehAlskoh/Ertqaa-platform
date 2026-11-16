@props(['applications', 'title' => 'أحدث طلبات الالتحاق', 'student' => false])
<x-dashboard-panel :title="$title" icon="file" description="أحدث 6 طلبات كحد أقصى؛ الحالة المعروضة تراعي انتهاء مهلة الحجز." :url="route($student ? 'student.applications.index' : 'admin.applications.index')">
    @if($applications->isNotEmpty())
        <div class="table-scroll" role="region" tabindex="0" aria-label="{{ $title }}، قابل للتمرير أفقيًا">
            <table class="data-table dashboard-data-table">
                <caption class="visually-hidden">{{ $title }}</caption>
                <thead><tr>
                    @if(!$student)<th scope="col">الطالب</th>@endif
                    <th scope="col">الكورس والمجموعة</th><th scope="col">الحالة</th><th scope="col">تاريخ الطلب</th><th scope="col">الإجراء</th>
                </tr></thead>
                <tbody>
                    @foreach($applications as $application)
                        <tr>
                            @if(!$student)<td><strong class="table-primary">{{ $application->student?->name }}</strong><p class="table-secondary">طلب #{{ $application->id }}</p></td>@endif
                            <td class="dashboard-course-cell"><strong class="table-primary">{{ $application->trainingGroup?->course?->title }}</strong><p class="table-secondary"><bdi>{{ $application->trainingGroup?->code }}</bdi>@if($student) · طلب #{{ $application->id }}@endif</p></td>
                            <td><x-application-badge :application="$application" /></td>
                            <td><x-date :value="$application->created_at" date-only /></td>
                            <td><a class="button button-secondary button-small" href="{{ route($student ? 'student.applications.show' : 'admin.applications.show', $application) }}">فتح الطلب</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-empty title="لا توجد طلبات للعرض" :description="$student ? 'ستظهر أحدث طلباتك هنا بعد التقديم من تفاصيل مجموعة منشورة.' : 'ستظهر أحدث طلبات الطلاب هنا عند تقديمها.'" />
    @endif
</x-dashboard-panel>
