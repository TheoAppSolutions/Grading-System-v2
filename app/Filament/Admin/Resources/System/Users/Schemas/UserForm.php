<?php

namespace App\Filament\Admin\Resources\System\Users\Schemas;

use App\Enums\Honorific;
use App\Enums\Suffix;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('')
                    ->schema([
                        Group::make([
                            Toggle::make('active')
                                ->inline(false)
                                ->onIcon(Heroicon::HandThumbUp)
                                ->offIcon(Heroicon::HandThumbDown),
                            Action::make('resetPassword')
                                ->label('Reset Password')
                                ->color('info')
                                ->icon('heroicon-o-key')
                                ->requiresConfirmation()
                                ->modalHeading('Reset Password')
                                ->modalDescription('Set a new password for this user.')
                                ->schema([
                                    TextInput::make('password')
                                        ->label('New Password')
                                        ->password()
                                        ->revealable()
                                        ->required()
                                        ->minLength(8)
                                        ->same('password_confirmation'),

                                    TextInput::make('password_confirmation')
                                        ->label('Confirm Password')
                                        ->password()
                                        ->revealable()
                                        ->required(),
                                ])
                                ->action(function (array $data, User $record) {
                                    $record->update([
                                        'password' => bcrypt($data['password']),
                                    ]);

                                    Notification::make()
                                        ->title('Password reset successfully')
                                        ->success()
                                        ->send();
                                }),
                        ])->columns(2),
                        FileUpload::make('photo')
                            ->label('User Image'),
                    ]),
                Tabs::make('information')
                    ->columnSpan(2)
                    ->schema([
                        Tab::make('Personal Information')
                            ->schema([
                                Select::make('honorifics')
                                    ->options(Honorific::class)
                                    ->columnSpan(1),
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->columnSpan(1),
                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->columnSpan(1),
                                Select::make('suffix')
                                    ->options(Suffix::groupedOptions())
                                    ->searchable()
                                    ->columnSpan(1),
                            ]),
                        Tab::make('Additional Information')
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
                                Textarea::make('address')
                                    ->columnSpan('full'),
                            ]),
                    ]),
            ]);
    }
}
