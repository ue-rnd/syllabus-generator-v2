<?php

namespace App\Filament\Admin\Resources\Setting\Pages;

use App\Filament\Admin\Resources\Setting\SettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSetting extends ManageRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
