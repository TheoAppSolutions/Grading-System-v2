<?php

namespace App\Filament\Admin\Resources\School\Sections\Pages;

use App\Filament\Admin\Resources\School\Sections\SectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSection extends ViewRecord
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
