<?php

namespace App\Filament\App\Resources\Deportments\Pages;

use App\Filament\App\Resources\Deportments\DeportmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeportment extends EditRecord
{
    protected static string $resource = DeportmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
