<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Pages;

use App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\FaqResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;
}