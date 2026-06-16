<?php

namespace App\Filament\Admin\Resources\System\Users\Pages;

use App\Filament\Admin\Resources\System\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
            DeleteAction::make()
                ->visible(Auth::id() != 1),
        ];
    }
}
