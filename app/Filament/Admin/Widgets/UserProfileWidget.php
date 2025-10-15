<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class UserProfileWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.user-profile-widget';

    /**
     * Make the profile area occupy most of the row on large screens,
     * and full width on small screens.
     */
    protected int|string|array $columnSpan = ['sm' => 'full', 'lg' => 6];

    protected static ?int $sort = 1;

    public function getData(): array
    {
        $user = auth()->user();

        return [
            'user' => $user,
            'college' => $user->college,
            'department' => $user->department,
        ];
    }
}
