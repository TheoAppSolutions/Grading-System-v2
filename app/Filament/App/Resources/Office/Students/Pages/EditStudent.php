<?php

namespace App\Filament\App\Resources\Office\Students\Pages;

use App\Filament\App\Resources\Office\Students\StudentResource;
use App\Support\Concerns\CleansUpPhotoFiles;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Override;

class EditStudent extends EditRecord
{
    use CleansUpPhotoFiles;

    protected static string $resource = StudentResource::class;

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return $this->record->first_name.' '.$this->record->middle_name.' '.$this->record->last_name;
    }

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
                ->visible(Auth::user()->isAdmin()),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        $this->cleanupUnreferencedPhotoFiles(
            'photos/students/'.$this->record->getKey(),
            $this->form->getRawState()['photo'] ?? null,
        );
    }
}
