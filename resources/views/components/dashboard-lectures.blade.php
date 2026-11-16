@props(['lectures', 'title' => 'محاضرات اليوم', 'description' => null, 'url', 'showInstructor' => true])
<x-dashboard-panel :title="$title" icon="calendar" :description="$description" :url="$url">
    @if($lectures->isNotEmpty())
        <div class="table-scroll" role="region" tabindex="0" aria-label="{{ $title }}، قابل للتمرير أفقيًا">
            <table class="data-table dashboard-data-table">
                <caption class="visually-hidden">{{ $title }} · توقيت طرابلس</caption>
                <thead><tr><th scope="col">المحاضرة والكورس</th>
                    @if($showInstructor)<th scope="col">المحاضر</th>@endif
                    <th scope="col">الموعد · طرابلس</th><th scope="col">الحالة</th><th scope="col">التفاصيل</th>
                </tr></thead>
                <tbody>
                    @foreach($lectures as $lecture)
                        <tr>
                            <td class="dashboard-course-cell"><strong class="table-primary">{{ $lecture->title ?: 'محاضرة رقم '.$lecture->lecture_number }}</strong><p class="table-secondary">{{ $lecture->trainingGroup?->course?->title }}</p><p class="table-secondary"><bdi>{{ $lecture->trainingGroup?->code }}</bdi> · محاضرة {{ $lecture->lecture_number }}</p></td>
                            @if($showInstructor)<td>{{ $lecture->trainingGroup?->instructor?->user?->name ?? 'غير محدد' }}</td>@endif
                            <td><div class="lecture-time-cell"><span>من <x-date :value="$lecture->starts_at" /></span><span>إلى <x-date :value="$lecture->ends_at" /></span></div></td>
                            <td><x-badge :status="$lecture->status" /></td>
                            <td><a class="button button-secondary button-small" href="{{ route('lectures.show', $lecture) }}">التفاصيل</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-empty title="لا توجد محاضرات للعرض" description="ستظهر المحاضرات المطابقة لهذا النطاق عند توفرها." />
    @endif
</x-dashboard-panel>
