<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Filament\Forms\Components\Select;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('id')
                    ->label('Username')
                    ->required()
                    ->autofocus()
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
            ])
            ->statePath('data');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $user = User::where('id', $data['id'])->first();

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
