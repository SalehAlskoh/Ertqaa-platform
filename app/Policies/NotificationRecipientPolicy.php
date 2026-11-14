<?php

namespace App\Policies;

use App\Models\NotificationRecipient;
use App\Models\User;

class NotificationRecipientPolicy
{
    public function update(User $user, NotificationRecipient $notificationRecipient): bool
    {
        return $user->is_active && $notificationRecipient->user_id === $user->id;
    }
}
