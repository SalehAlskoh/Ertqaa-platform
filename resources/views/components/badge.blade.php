@props(['status'])
@php($labels = ['draft'=>'مسودة','published'=>'منشورة','in_progress'=>'قيد التنفيذ','completed'=>'مكتملة','cancelled'=>'ملغاة','active'=>'نشط','inactive'=>'معطّل','scheduled'=>'مجدولة','pending'=>'قيد المراجعة'])
<span class="badge badge-{{ $status }}">{{ $labels[$status] ?? $status }}</span>
