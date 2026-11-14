@props(['application'])
@php($labels = ['pending'=>'قيد المراجعة','awaiting_confirmation'=>'بانتظار التأكيد الحضوري','rejected'=>'مرفوض','expired'=>'انتهت المهلة','cancelled'=>'ملغى','completed'=>'مكتمل'])
<x-badge :status="$application->effective_status" :label="$labels[$application->effective_status] ?? $application->effective_status" />
