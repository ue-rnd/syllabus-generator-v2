<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotificationsWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.notifications-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    public function getViewData(): array
    {
        $user = Auth::user();

        // Get unread notifications for the current user
        $notifications = $user->unreadNotifications()
            ->limit(10)
            ->get();

        return [
            'notifications' => $notifications,
            'hasNotifications' => $notifications->isNotEmpty(),
        ];
    }
}
