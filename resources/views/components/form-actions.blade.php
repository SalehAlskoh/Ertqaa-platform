@props(['cancel', 'label' => 'حفظ التغييرات'])
<div class="form-actions"><x-submit :label="$label" icon="check" /><a class="button button-secondary" href="{{ $cancel }}">إلغاء والعودة</a></div>
