@props(['status', 'label' => null])
@php($labels = ['draft'=>'مسودة','published'=>'منشورة','in_progress'=>'قيد التنفيذ','completed'=>'مكتملة','cancelled'=>'ملغاة','active'=>'نشط','inactive'=>'معطّل','scheduled'=>'مجدولة','pending'=>'قيد المراجعة','awaiting_confirmation'=>'بانتظار التأكيد الحضوري','rejected'=>'مرفوض','expired'=>'انتهت المهلة','withdrawn'=>'منسحب'])
<span {{ $attributes->class(['badge', 'badge-'.$status]) }}><span class="badge-dot" aria-hidden="true"></span>{{ $label ?? $labels[$status] ?? $status }}</span>
