<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Roles\Pages;

use App\Filament\Admin\Clusters\UserManagement\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
