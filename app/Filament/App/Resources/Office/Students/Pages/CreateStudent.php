<?php

namespace App\Filament\App\Resources\Office\Students\Pages;

use App\Filament\App\Resources\Office\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
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

    protected function afterCreate(): void
    {
        $pendingPath = $this->form->getRawState()['photo'] ?? $this->data['photo'] ?? null;

        if (! is_string($pendingPath) || ! str_starts_with($pendingPath, 'photos/students/pending/')) {
            return;
        }

        $targetPath = 'photos/students/' . $this->record->getKey() . '/' . basename($pendingPath);

        Storage::disk('public')->move($pendingPath, $targetPath);
    }
}
