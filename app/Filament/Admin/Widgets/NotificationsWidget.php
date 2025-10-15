<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotificationsWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.notifications-widget';

    /**
     * Make the widget full width on small screens and span 4 columns on large screens
     * so it pairs with the profile widget (6 + 4 = 10 columns on large screens).
     */
    protected int|string|array $columnSpan = ['sm' => 'full', 'lg' => 4];

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
