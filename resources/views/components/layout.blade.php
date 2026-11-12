@props(['title' => 'ارتقاء'])
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
<body>
    <a href="#main-content" class="visually-hidden focus:not-sr-only focus:fixed focus:top-2 focus:right-2 focus:z-50 button">انتقل إلى المحتوى</a>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="ارتقاء — الصفحة الرئيسية">
                <span class="brand-mark" aria-hidden="true"><i></i><i></i><i></i></span>
                <span><span class="brand-name">ارتقاء</span><span class="brand-subtitle">معرفة تُنمّي طموحك</span></span>
            </a>
            <nav class="primary-nav" id="primary-navigation" aria-label="التنقل الرئيسي">
                <a href="{{ route('courses.index') }}" class="nav-link" @if(request()->routeIs('courses.*')) aria-current="page" @endif>الكورسات</a>
                <a href="{{ route('groups.index') }}" class="nav-link" @if(request()->routeIs('groups.*')) aria-current="page" @endif>المجموعات التدريبية</a>
                @can('teach')
                    <a href="{{ route('instructor.groups.index') }}" class="nav-link" @if(request()->routeIs('instructor.groups.*')) aria-current="page" @endif>مجموعاتي</a>
                    <a href="{{ route('instructor.profile') }}" class="nav-link" @if(request()->routeIs('instructor.profile')) aria-current="page" @endif>ملفي المهني</a>
                @endcan
            </nav>
            <div class="account-nav">
                @auth
                    <a href="{{ route('profile.edit') }}" class="nav-link" @if(request()->routeIs('profile.*')) aria-current="page" @endif>حسابي</a>
                    <form action="{{ route('logout') }}" method="post">@csrf<button class="button button-secondary button-small" type="submit">خروج</button></form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">دخول</a>
                    <a href="{{ route('register') }}" class="button button-small">حساب طالب جديد</a>
                @endauth
                <button type="button" class="button button-secondary button-small menu-toggle" data-menu-toggle aria-controls="primary-navigation" aria-expanded="false" aria-label="فتح قائمة التنقل">☰</button>
            </div>
        </div>
    </header>
    @can('manage-catalog')
        <nav class="management-nav" aria-label="إدارة المركز">
            <div class="management-inner">
                <span class="nav-label">إدارة المركز</span>
                <a class="nav-link" href="{{ route('admin.courses.index') }}" @if(request()->routeIs('admin.courses.*')) aria-current="page" @endif>الكورسات</a>
                <a class="nav-link" href="{{ route('admin.course-categories.index') }}" @if(request()->routeIs('admin.course-categories.*')) aria-current="page" @endif>التصنيفات</a>
                <a class="nav-link" href="{{ route('admin.training-groups.index') }}" @if(request()->routeIs('admin.training-groups.*')) aria-current="page" @endif>المجموعات</a>
                @can('manage-accounts')
                    <a class="nav-link" href="{{ route('admin.instructors.index') }}" @if(request()->routeIs('admin.instructors.*')) aria-current="page" @endif>المحاضرون</a>
                    <a class="nav-link" href="{{ route('admin.staff.index') }}" @if(request()->routeIs('admin.staff.*')) aria-current="page" @endif>الموظفون</a>
                @endcan
            </div>
        </nav>
    @endcan
    <main class="page-shell" id="main-content">
        @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())
            <div class="notice notice-error" role="alert"><p class="font-bold">يرجى مراجعة البيانات التالية</p><ul class="mt-2 list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        {{ $slot }}
    </main>
    <footer class="site-footer"><div class="footer-inner"><span>ارتقاء · مساحة للتعلّم والنمو</span><span>© {{ now()->year }} — جميع الحقوق محفوظة</span></div></footer>
</body>
</html>
