<?php

namespace App\Filament\App\Resources\Office\Subjects\Pages;

use App\Filament\App\Resources\Office\Subjects\SubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Override;

class EditSubject extends EditRecord
{
    protected static string $resource = SubjectResource::class;

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
