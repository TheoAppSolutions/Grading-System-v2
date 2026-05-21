<?php

namespace App\Filament\App\Resources\Office\Graduates\Pages;

use App\Filament\App\Resources\Office\Graduates\GraduateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGraduate extends EditRecord
{
    protected static string $resource = GraduateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
