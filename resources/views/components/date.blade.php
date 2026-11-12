@props(['value', 'dateOnly' => false])
@if($value)<time datetime="{{ \Illuminate\Support\Carbon::parse($value)->toIso8601String() }}" dir="ltr">{{ \Illuminate\Support\Carbon::parse($value)->timezone('Africa/Tripoli')->format($dateOnly ? 'Y/m/d' : 'Y/m/d · H:i') }}</time>@else<span>غير محدد</span>@endif
