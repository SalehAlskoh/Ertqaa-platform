@props(['value', 'dateOnly' => false])
@php($date = $value ? \Illuminate\Support\Carbon::parse($value) : null)
@if($date)
    <time {{ $attributes->class(['date-value']) }} datetime="{{ $date->toIso8601String() }}" dir="ltr">{{ $date->copy()->timezone('Africa/Tripoli')->format($dateOnly ? 'Y/m/d' : 'Y/m/d · H:i') }}</time>
@else
    <span>غير محدد</span>
@endif
