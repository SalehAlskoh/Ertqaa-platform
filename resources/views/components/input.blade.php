@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null])
<div class="field">
    <label class="field-label" for="{{ $attributes->get('id', $name) }}">{{ $label }}@if($required)<span class="required-mark" aria-hidden="true">*</span>@endif</label>
    <input {{ $attributes->class(['input']) }} id="{{ $attributes->get('id', $name) }}" name="{{ $name }}" type="{{ $type }}" @if($type !== 'password') value="{{ old($name, $value) }}" @endif @required($required) @if($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
    @if($help)<p class="field-help">{{ $help }}</p>@endif
    @error($name)<p class="field-error" id="{{ $name }}-error">{{ $message }}</p>@enderror
</div>
