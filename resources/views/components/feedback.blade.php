@if(session('status'))
    <div class="notice" role="status"><x-icon name="check-circle" /><span>{{ session('status') }}</span></div>
@endif
@if($errors->any())
    <div class="notice notice-error" role="alert" tabindex="-1">
        <x-icon name="alert" />
        <div><p class="notice-title">يرجى مراجعة البيانات التالية</p><ul class="feedback-errors">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    </div>
@endif
