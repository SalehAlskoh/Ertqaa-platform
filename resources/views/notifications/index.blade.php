<x-layout title="الإشعارات">
    <x-page-heading title="إشعاراتي" description="إشعارات حسابك داخل المنصة بشأن الطلبات والمحاضرات والتسجيل." />
    @if($notifications->count())
        <div class="notification-list">
            @foreach($notifications as $notification)
                <article @class(['panel','notification-card','is-unread'=>!$notification->read_at])>
                    <span class="notification-marker" aria-hidden="true"><x-icon name="bell" :size="19" /></span>
                    <div class="notification-content"><h2>{{ $notification->message?->title ?? 'إشعار' }}</h2><p>{{ $notification->message?->body }}</p><div class="notification-meta"><x-date :value="$notification->message?->created_at" />
                        @if($notification->read_at)
                            <span>مقروء · <x-date :value="$notification->read_at" /></span>
                        @else
                            <span class="notification-unread">غير مقروء</span>
                        @endif
                    </div></div>
                    @if(!$notification->read_at)
                        @can('update', $notification)
                            <form action="{{ route('notifications.read', $notification) }}" method="post">
                                @csrf
                                @method('PATCH')
                                <button class="button button-secondary button-small" type="submit"><x-icon name="check" :size="15" />تحديد كمقروء</button>
                            </form>
                        @endcan
                    @endif
                </article>
            @endforeach
        </div>
        @if($notifications->hasPages())
            <div class="pagination">{{ $notifications->links() }}</div>
        @endif
    @else
        <section class="panel"><x-empty title="لا توجد إشعارات بعد" description="ستظهر تحديثات حسابك هنا عند توفرها. لا تحتاج إلى أي إعداد إضافي." /></section>
    @endif
</x-layout>
