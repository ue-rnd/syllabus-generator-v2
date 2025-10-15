<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class UserProfileWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.user-profile-widget';

    protected int|string|array $columnSpan = 'full';

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
