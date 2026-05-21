<?php

namespace App\Filament\Admin\Resources\School\Sections\Pages;

use App\Filament\Admin\Resources\School\Sections\SectionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateSection extends CreateRecord
{
    protected static string $resource = SectionResource::class;

    
    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')->hidden();
    }
}
