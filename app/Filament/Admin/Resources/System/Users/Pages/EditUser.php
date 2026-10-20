<?php

namespace App\Filament\Admin\Resources\System\Users\Pages;

use App\Filament\Admin\Resources\System\Users\UserResource;
use App\Support\Concerns\CleansUpPhotoFiles;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    use CleansUpPhotoFiles;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Record')
                ->color('primary')
                ->action('save'),

            Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->keyBindings(['escape']),

            DeleteAction::make()
                ->visible(Auth::id() != 1),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        $this->cleanupUnreferencedPhotoFiles(
            'photos/'.$this->record->getKey(),
            $this->form->getRawState()['photo'] ?? null,
        );
    }
}
