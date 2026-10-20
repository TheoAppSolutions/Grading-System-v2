<?php

namespace App\Filament\Pages\Auth;

use App\Enums\Honorific;
use App\Enums\Suffix;
use App\Models\User;
use App\Support\Concerns\CleansUpPhotoFiles;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    use CleansUpPhotoFiles;

    protected function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Profile Details')
                    ->schema([
                        FileUpload::make('photo')
                            ->label('Profile photo')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->disk('public')
                            ->directory(fn (): string => 'photos/'.auth()->id())
                            ->visibility('public')
                            ->default(fn (?User $record): ?string => $record?->photo_path)
                            ->afterStateHydrated(function (FileUpload $component, ?User $record): void {
                                if (blank($component->getState()) && $record?->photo_path) {
                                    $component->state($record->photo_path);
                                }
                            })
                            ->columnSpanFull(),
                        Select::make('honorifics')
                            ->options(Honorific::class)
                            ->columnSpan(1),
                        Select::make('suffix')
                            ->options(Suffix::groupedOptions())
                            ->searchable()
                            ->columnSpan(1),
                        TextInput::make('first_name')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('last_name')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('middle_name')
                            ->columnSpan(1),
                    ]),
                Section::make('Additional Details')
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->columnSpan(1),
                        DatePicker::make('birth_date')
                            ->columnSpan(1),
                        TextInput::make('contact')
                            ->tel()
                            ->columnSpan(1),
                        Textarea::make('address'),
                    ]),
                Section::make('Change Password')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }

    protected function afterSave(): void
    {
        $this->cleanupUnreferencedPhotoFiles(
            'photos/'.$this->getUser()->getKey(),
            $this->form->getRawState()['photo'] ?? null,
        );
    }
}
