@props(['capacity', 'occupied', 'reserved'])
<dl class="seat-summary-grid">
    <div><dt>السعة الكلية</dt><dd>{{ $capacity }}</dd></div>
    <div><dt>مشغول</dt><dd>{{ $occupied }}</dd></div>
    <div><dt>محجوز مؤقتًا</dt><dd>{{ $reserved }}</dd></div>
    <div><dt>متاح</dt><dd>{{ max(0, $capacity - $occupied - $reserved) }}</dd></div>
</dl>
