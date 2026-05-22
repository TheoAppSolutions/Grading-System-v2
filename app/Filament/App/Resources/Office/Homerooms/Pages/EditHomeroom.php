<?php

namespace App\Filament\App\Resources\Office\Homerooms\Pages;

use App\Filament\App\Resources\Office\Homerooms\HomeroomResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditHomeroom extends EditRecord
{
    protected static string $resource = HomeroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[Override]
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url($this->getResource()::getUrl('index'))
            ->color('secondary');
    }
}
