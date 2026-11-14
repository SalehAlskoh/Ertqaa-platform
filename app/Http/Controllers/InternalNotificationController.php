<?php

namespace App\Http\Controllers;

use App\Models\NotificationRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InternalNotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('notifications.index', ['notifications' => NotificationRecipient::where('user_id', $request->user()->id)->with('message')->latest('id')->paginate(15)]);
    }

    public function read(NotificationRecipient $notificationRecipient)
    {
        Gate::authorize('update', $notificationRecipient);
        NotificationRecipient::whereKey($notificationRecipient->id)->whereNull('read_at')->update(['read_at' => now()]);

        return back()->setStatusCode(303)->with('status', 'عُلّم الإشعار كمقروء.');
    }
}
