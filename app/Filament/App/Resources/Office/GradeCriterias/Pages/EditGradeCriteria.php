<?php

namespace App\Filament\App\Resources\Office\GradeCriterias\Pages;

use App\Filament\App\Resources\Office\GradeCriterias\GradeCriteriaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGradeCriteria extends EditRecord
{
    protected static string $resource = GradeCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
