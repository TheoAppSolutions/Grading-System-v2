<?php

namespace App\Filament\App\Resources\Office\Graduates\Pages;

use App\Filament\App\Resources\Office\Graduates\GraduateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGraduates extends ListRecords
{
    protected static string $resource = GraduateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
