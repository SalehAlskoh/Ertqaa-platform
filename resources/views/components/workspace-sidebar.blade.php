<aside class="workspace-sidebar" id="workspace-sidebar" data-workspace-drawer aria-label="القائمة الجانبية" tabindex="-1">
    <div class="sidebar-brand"><x-brand /><button class="icon-button sidebar-close" type="button" data-drawer-close aria-label="إغلاق القائمة"><x-icon name="close" /></button></div>
    <div class="sidebar-section-label">مساحة العمل</div>
    <nav class="sidebar-nav" aria-label="تنقل مساحة العمل">
        @can('manage-catalog')
            <a class="sidebar-link" href="{{ route('admin.courses.index') }}" @if(request()->routeIs('admin.courses.*')) aria-current="page" @endif><x-icon name="book" /><span>إدارة الكورسات</span></a>
            <a class="sidebar-link" href="{{ route('admin.course-categories.index') }}" @if(request()->routeIs('admin.course-categories.*')) aria-current="page" @endif><x-icon name="tag" /><span>التصنيفات</span></a>
            <a class="sidebar-link" href="{{ route('admin.training-groups.index') }}" @if(request()->routeIs('admin.training-groups.*')) aria-current="page" @endif><x-icon name="users" /><span>المجموعات التدريبية</span></a>
            <a class="sidebar-link" href="{{ route('admin.lectures.index') }}" @if(request()->routeIs('admin.lectures.*')) aria-current="page" @endif><x-icon name="calendar" /><span>إدارة المحاضرات</span></a>
            <a class="sidebar-link" href="{{ route('admin.applications.index') }}" @if(request()->routeIs('admin.applications.*')) aria-current="page" @endif><x-icon name="file" /><span>طلبات التسجيل</span></a>
        @endcan
        @can('manage-accounts')
            <a class="sidebar-link" href="{{ route('admin.instructors.index') }}" @if(request()->routeIs('admin.instructors.*')) aria-current="page" @endif><x-icon name="user" /><span>المحاضرون</span></a>
            <a class="sidebar-link" href="{{ route('admin.staff.index') }}" @if(request()->routeIs('admin.staff.*')) aria-current="page" @endif><x-icon name="briefcase" /><span>حسابات الموظفين</span></a>
        @endcan
        @can('teach')
            <a class="sidebar-link" href="{{ route('instructor.groups.index') }}" @if(request()->routeIs('instructor.groups.*')) aria-current="page" @endif><x-icon name="users" /><span>مجموعاتي التدريبية</span></a>
            <a class="sidebar-link" href="{{ route('instructor.profile') }}" @if(request()->routeIs('instructor.profile')) aria-current="page" @endif><x-icon name="briefcase" /><span>ملفي المهني</span></a>
            <a class="sidebar-link" href="{{ route('instructor.lectures.index') }}" @if(request()->routeIs('instructor.lectures.*')) aria-current="page" @endif><x-icon name="calendar" /><span>محاضرات مجموعاتي</span></a>
        @endcan
        @if(auth()->user()?->hasRole('student'))
            <a class="sidebar-link" href="{{ route('student.applications.index') }}" @if(request()->routeIs('student.applications.*')) aria-current="page" @endif><x-icon name="file" /><span>طلباتي</span></a>
            <a class="sidebar-link" href="{{ route('student.enrollments.index') }}" @if(request()->routeIs('student.enrollments.*')) aria-current="page" @endif><x-icon name="book" /><span>تسجيلاتي</span></a>
            <a class="sidebar-link" href="{{ route('student.lectures.index') }}" @if(request()->routeIs('student.lectures.*')) aria-current="page" @endif><x-icon name="calendar" /><span>محاضراتي</span></a>
        @endif
        <a class="sidebar-link" href="{{ route('notifications.index') }}" @if(request()->routeIs('notifications.*')) aria-current="page" @endif><x-icon name="bell" /><span>إشعاراتي</span></a>
    </nav>
    <div class="sidebar-bottom">
        <a class="sidebar-link" href="{{ route('courses.index') }}"><x-icon name="home" /><span>الموقع العام</span><x-icon name="arrow-left" :size="17" class="sidebar-trailing" /></a>
        <a class="sidebar-link" href="{{ route('profile.edit') }}"><x-icon name="user" /><span>حسابي</span></a>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button class="sidebar-link sidebar-logout" type="submit"><x-icon name="logout" /><span>تسجيل الخروج</span></button>
        </form>
        <p class="sidebar-footer">ارتقاء · معرفة تُنمّي طموحك</p>
    </div>
</aside>
<button type="button" class="drawer-backdrop" data-drawer-backdrop hidden tabindex="-1" aria-label="إغلاق القائمة الجانبية"></button>
