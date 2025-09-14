<?php

namespace App\Filament\Admin\Clusters\UserManagement\Resources\Users\Pages;

use App\Filament\Admin\Clusters\UserManagement\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
