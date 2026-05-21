<?php

namespace App\Filament\Admin\Resources\System\Logs\Pages;

use App\Filament\Admin\Resources\System\Logs\LogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLogs extends ListRecords
{
    protected static string $resource = LogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
