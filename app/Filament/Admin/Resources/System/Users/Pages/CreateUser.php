<?php

namespace App\Filament\Admin\Resources\System\Users\Pages;

use App\Filament\Admin\Resources\System\Users\UserResource;
use App\Models\Configuration;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Override;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $defaultPassword = Configuration::get('default_password');

        if (! is_string($defaultPassword) || trim($defaultPassword) === '') {
            throw ValidationException::withMessages([
                'default_password' => 'A default password must be configured before creating a user.',
            ]);
        }

        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['active'] = 1;
        $data['password'] = bcrypt($defaultPassword);
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
