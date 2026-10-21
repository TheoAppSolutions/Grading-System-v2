<?php

namespace App\Filament\Admin\Resources\System\Users\Pages;

use App\Filament\Admin\Resources\System\Users\UserResource;
use App\Models\Configuration;
use Filament\Resources\Pages\CreateRecord;
use Override;
use Illuminate\Support\Facades\Storage;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['active'] = 1;
        $data['password'] = bcrypt(Configuration::get('default_password', '654321'));
        return parent::mutateFormDataBeforeCreate($data);
    }

    protected function afterCreate(): void
    {
        $pendingPath = $this->data['photo'] ?? null;

        if (! is_string($pendingPath) || ! str_starts_with($pendingPath, 'photos/pending/')) {
            return;
        }

        $targetPath = 'photos/' . $this->record->getKey() . '/' . basename($pendingPath);

        Storage::disk('public')->move($pendingPath, $targetPath);
    }
}
