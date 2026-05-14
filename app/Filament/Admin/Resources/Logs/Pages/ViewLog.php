<?php

namespace App\Filament\Admin\Resources\Logs\Pages;

use App\Filament\Admin\Resources\Logs\LogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLog extends ViewRecord
{
    protected static string $resource = LogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
