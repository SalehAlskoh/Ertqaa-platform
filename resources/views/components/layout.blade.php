@props(['title' => 'ارتقاء'])
@php($workspace = request()->routeIs('admin.*', 'staff.*', 'instructor.*', 'student.*', 'notifications.*', 'lectures.*'))
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ارتقاء، منصة المركز التدريبي لاستكشاف الكورسات والمجموعات التدريبية وإدارة مسيرتك التعليمية.">
    <title>{{ $title }} | ارتقاء</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $workspace ? 'workspace' : 'public-site' }}">
    <a href="#main-content" class="skip-link button">انتقل إلى المحتوى</a>
    @if($workspace)
        <x-workspace-sidebar />
        <noscript><style>.workspace-sidebar { position:static; width:auto; visibility:visible; } .workspace-content { margin-inline-start:0; } .drawer-toggle,.sidebar-close { display:none; }</style></noscript>
        <div class="workspace-content" data-drawer-background>
            <x-workspace-topbar :title="$title" />
            <main class="page-shell" id="main-content">
                <x-feedback />
                {{ $slot }}
            </main>
            <footer class="site-footer"><div class="footer-inner"><span>منصة ارتقاء · إدارة المركز التدريبي</span><span>© {{ now()->year }} جميع الحقوق محفوظة</span></div></footer>
        </div>
    @else
        <x-public-navigation />
        <main class="page-shell" id="main-content">
            <x-feedback />
            {{ $slot }}
        </main>
        <footer class="site-footer"><div class="footer-inner"><span>ارتقاء · مساحة للتعلّم والنمو</span><span>© {{ now()->year }} جميع الحقوق محفوظة</span></div></footer>
    @endif
    <x-confirmation-dialog />
</body>
</html>
