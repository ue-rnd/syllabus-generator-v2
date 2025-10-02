<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\TutorialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTutorial extends CreateRecord
{
    protected static string $resource = TutorialResource::class;
}