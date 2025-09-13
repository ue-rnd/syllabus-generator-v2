<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users\Pages;

use App\Filament\Admin\Clusters\UserManagement\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
