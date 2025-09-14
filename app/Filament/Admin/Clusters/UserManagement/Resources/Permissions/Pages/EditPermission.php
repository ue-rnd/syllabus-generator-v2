<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\Pages;

use App\Filament\Admin\Clusters\UserManagement\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;
}
