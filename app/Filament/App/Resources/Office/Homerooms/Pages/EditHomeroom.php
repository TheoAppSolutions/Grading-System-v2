<?php

namespace App\Filament\App\Resources\Office\Homerooms\Pages;

use App\Filament\App\Resources\Office\Homerooms\HomeroomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeroom extends EditRecord
{
    protected static string $resource = HomeroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
