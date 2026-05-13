<?php

namespace App\Filament\App\Resources\Graduates\Pages;

use App\Filament\App\Resources\Graduates\GraduateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGraduate extends ViewRecord
{
    protected static string $resource = GraduateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
