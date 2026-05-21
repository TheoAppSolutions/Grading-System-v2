<?php

namespace App\Filament\App\Resources\Homeroom\Deportments\Pages;

use App\Filament\App\Resources\Homeroom\Deportments\DeportmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeportments extends ListRecords
{
    protected static string $resource = DeportmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
