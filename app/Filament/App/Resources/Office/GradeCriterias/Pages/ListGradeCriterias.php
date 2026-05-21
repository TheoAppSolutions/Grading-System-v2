<?php

namespace App\Filament\App\Resources\Office\GradeCriterias\Pages;

use App\Filament\App\Resources\Office\GradeCriterias\GradeCriteriaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGradeCriterias extends ListRecords
{
    protected static string $resource = GradeCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
