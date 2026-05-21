<?php

namespace App\Filament\App\Resources\Office\GradeCriterias\Pages;

use App\Filament\App\Resources\Office\GradeCriterias\GradeCriteriaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGradeCriteria extends ViewRecord
{
    protected static string $resource = GradeCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
