<?php

namespace App\Filament\App\Resources\Office\Grades\Pages;

use App\Filament\App\Resources\Office\Grades\GradeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGrade extends EditRecord
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
