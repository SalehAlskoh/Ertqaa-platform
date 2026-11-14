@props(['name', 'label', 'value' => null, 'required' => false, 'help' => null])
@php
    $fieldId = $attributes->get('id', $name);
    $descriptions = implode(' ', array_filter([$attributes->get('aria-describedby'), $help ? $fieldId.'-help' : null, $errors->has($name) ? $fieldId.'-error' : null]));
@endphp
<div class="field">
    <label class="field-label" for="{{ $fieldId }}">{{ $label }}@if($required)<span class="required-mark" aria-hidden="true">*</span>@endif</label>
    <textarea {{ $attributes->except(['id', 'aria-describedby'])->class(['input']) }} id="{{ $fieldId }}" name="{{ $name }}" @required($required)
        @if($errors->has($name)) aria-invalid="true" @endif
        @if($descriptions) aria-describedby="{{ $descriptions }}" @endif>{{ old($name, $value) }}</textarea>
    @if($help)
        <p class="field-help" id="{{ $fieldId }}-help">{{ $help }}</p>
    @endif
    @error($name)
        <p class="field-error" id="{{ $fieldId }}-error">{{ $message }}</p>
    @enderror
</div>
