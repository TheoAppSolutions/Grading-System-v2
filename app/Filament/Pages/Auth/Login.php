<?php

namespace App\Filament\Pages\Auth;

use App\Helpers\MyTools;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Panel;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        $hasUpdate = MyTools::fetchGit(forceFetch: true);
        return $schema
            ->schema([
                TextEntry::make('label')
                    ->label('Enter Admin Password')
                    ->visible(fn() => Filament::getCurrentPanel()?->getId() == 'admin'),
                Select::make('id')
                    ->label('Username')
                    ->required()
                    ->autofocus()
                    ->visible(fn() => Filament::getCurrentPanel()?->getId() != 'admin')
                    ->options(
                        User::where('active', true)
                            ->pluck('name', 'id')
                            ->mapWithKeys(fn($value, $key) => [
                                $key => $key === 1 ? 'Admin' : $value
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->extraInputAttributes(['tabindex' => 1]),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                TextEntry::make('updater')
                    ->visible($hasUpdate ? true : false)
                    ->label($hasUpdate ? "New Update Available" : ""),
            ])
            ->statePath('data');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        if (Filament::getCurrentPanel()?->getId() == 'admin') {
            $user = User::where('id', 1)->first();
        } else {
            $user = User::where('id', $data['id'])->first();
        }

        return [
            'id'    => $user?->id ?? $data['id'], // fallback gracefully
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.password' => "The provided password is incorrect.",
        ]);
    }
}
