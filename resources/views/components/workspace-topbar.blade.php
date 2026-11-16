@props(['title'])
@php
    $account = auth()->user();
    $roleLabels = ['admin' => 'مدير المنصة', 'staff' => 'موظف المركز', 'instructor' => 'محاضر', 'student' => 'طالب'];
    $accountRole = $account && $account->relationLoaded('roles') ? $account->roles->pluck('name')->first() : null;
@endphp
<header class="workspace-topbar">
    <div class="topbar-context">
        <button class="icon-button drawer-toggle" type="button" data-drawer-open aria-expanded="false" aria-controls="workspace-sidebar" aria-label="فتح القائمة الجانبية"><x-icon name="menu" :size="22" /></button>
        <a class="topbar-home" href="{{ route('dashboard') }}"><x-icon name="home" :size="18" /><span>لوحة التحكم</span></a>
        <x-icon name="chevron-left" :size="15" class="topbar-divider" />
        <span class="topbar-current">{{ $title }}</span>
    </div>
    <a href="{{ route('profile.edit') }}" class="topbar-account" aria-label="حسابي، {{ $account?->name }}">
        <span class="account-avatar">{{ mb_substr($account?->name ?? 'ح', 0, 1) }}</span>
        <span class="account-copy"><strong>{{ $account?->name }}</strong><span>{{ $roleLabels[$accountRole] ?? 'حسابي' }}</span></span>
        <x-icon name="user" :size="18" class="account-profile-icon" />
    </a>
</header>
