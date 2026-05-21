<?php

namespace App\Filament\App\Resources\Office\Homerooms\Pages;

use App\Filament\App\Resources\Office\Homerooms\HomeroomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomerooms extends ListRecords
{
    protected static string $resource = HomeroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
