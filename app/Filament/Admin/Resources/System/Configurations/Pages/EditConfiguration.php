<?php

namespace App\Filament\Admin\Resources\System\Configurations\Pages;

use App\Filament\Admin\Resources\System\Configurations\ConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConfiguration extends EditRecord
{
    protected static string $resource = ConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
