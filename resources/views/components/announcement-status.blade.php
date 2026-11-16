@props(['announcement'])
@php($labels = ['draft'=>'مسودة','published'=>'منشور','scheduled'=>'نشر مستقبلي'])
<x-badge :status="$announcement->publication_status" :label="$labels[$announcement->publication_status] ?? $announcement->publication_status" />
