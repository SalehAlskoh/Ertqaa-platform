@props(['notifications'])
<x-dashboard-panel title="أحدث إشعاراتي" icon="bell" description="آخر 5 إشعارات موجهة إلى حسابك فقط." :url="route('notifications.index')">
    @if($notifications->isNotEmpty())
        <div class="dashboard-notification-list">
            @foreach($notifications as $notification)
                <article @class(['dashboard-notification','is-unread'=>!$notification->read_at])>
                    <div class="dashboard-notification-title"><h3>{{ $notification->message?->title ?? 'إشعار' }}</h3>
                        @if(!$notification->read_at)<span class="dashboard-unread-label">غير مقروء</span>@endif
                    </div>
                    <p>{{ \Illuminate\Support\Str::limit($notification->message?->body ?? '', 180) }}</p>
                    <x-date :value="$notification->message?->created_at" />
                </article>
            @endforeach
        </div>
    @else
        <x-empty title="لا توجد إشعارات بعد" description="ستظهر تحديثات حسابك هنا عند توفرها." />
    @endif
</x-dashboard-panel>
