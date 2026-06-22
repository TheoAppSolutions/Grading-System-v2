<?php

namespace App\Filament\Admin\Resources\System\Users\Schemas;

use App\Enums\Honorific;
use App\Enums\Suffix;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('')
                    ->schema([
                        Toggle::make('active'),
                        FileUpload::make('photo')
                            ->label('User Image')
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
                            ])
                    ]),
            ]);
    }
}
