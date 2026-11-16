<header class="site-header">
    <div class="header-inner">
        <x-brand />
        <nav class="primary-nav" id="primary-navigation" aria-label="التنقل الرئيسي">
            <a href="{{ route('courses.index') }}" class="nav-link" @if(request()->routeIs('courses.*')) aria-current="page" @endif>الكورسات</a>
            <a href="{{ route('groups.index') }}" class="nav-link" @if(request()->routeIs('groups.*')) aria-current="page" @endif>المجموعات التدريبية</a>
            @auth
                <a href="{{ route('dashboard') }}" class="nav-link"><x-icon name="grid" :size="16" />لوحة التحكم</a>
            @endauth
            @can('manage-catalog')
                <a href="{{ route('admin.courses.index') }}" class="nav-link">إدارة المركز <x-icon name="arrow-left" :size="15" /></a>
            @endcan
            @can('teach')
                <a href="{{ route('instructor.groups.index') }}" class="nav-link">مجموعاتي</a>
                <a href="{{ route('instructor.profile') }}" class="nav-link">ملفي المهني</a>
            @endcan
            @if(auth()->user()?->hasRole('student'))
                <a href="{{ route('student.applications.index') }}" class="nav-link">طلباتي</a>
                <a href="{{ route('student.enrollments.index') }}" class="nav-link">تسجيلاتي</a>
            @endif
        </nav>
        <div class="account-nav">
            @auth
                <a href="{{ route('notifications.index') }}" class="icon-button" aria-label="إشعاراتي" title="إشعاراتي"><x-icon name="bell" :size="19" /></a>
                <a href="{{ route('profile.edit') }}" class="nav-link" @if(request()->routeIs('profile.*')) aria-current="page" @endif><x-icon name="user" :size="18" /><span>حسابي</span></a>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="icon-button" type="submit" aria-label="تسجيل الخروج" title="تسجيل الخروج"><x-icon name="logout" :size="19" /></button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link">دخول</a>
                <a href="{{ route('register') }}" class="button button-small">حساب طالب جديد</a>
            @endauth
            <button type="button" class="icon-button menu-toggle" data-menu-toggle aria-controls="primary-navigation" aria-expanded="false" aria-label="فتح قائمة التنقل"><x-icon name="menu" :size="22" /></button>
        </div>
    </div>
</header>
<noscript><style>.primary-nav { display:flex !important; } .menu-toggle { display:none !important; }</style></noscript>
