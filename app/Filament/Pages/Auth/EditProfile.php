<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Profile Details')
                    ->schema([
                        Select::make('honorifics')
                            ->options([
                                'Mr.'  => 'Mr.',
                                'Ms.'  => 'Ms.',
                                'Mrs.' => 'Mrs.',
                            ])
                            ->columnSpan(1),
                        Select::make('suffix')
                            ->options(User::getSuffixList())
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
}
