<?php

namespace App\Filament\App\Resources\Students\Pages;

use App\Filament\App\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['active'] = 1;
        return parent::mutateFormDataBeforeCreate($data);
    }
}
