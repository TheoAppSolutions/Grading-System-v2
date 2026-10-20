<?php

namespace App\Filament\App\Resources\Office\Students\Schemas;

use App\Enums\Gender;
use App\Enums\Suffix;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->columns(1)
                    ->schema([
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Toggle::make('active')
                                            ->default(true)
                                            ->visible(false),
                                        TextInput::make('id')
                                            ->label('Student ID')
                                            ->inlineLabel()
                                            ->required()
                                            ->numeric(),
                                    ]),
                                TextInput::make('student_lrn')
                                    ->label('Student LRN')
                                    ->visible(config('system-features.student_lrn')),
                            ]),
                        FileUpload::make('photo')
                            ->label('Student Photo')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->disk('public')
                            ->directory('students/photos')
                            ->visibility('public')
                            ->columnSpan(1),
                    ]),
                Tabs::make()
                    ->columnSpan(2)
                    ->schema([
                        Tab::make('Personal Information')
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required(),
                                TextInput::make('middle_name')
                                    ->label('Middle Name'),
                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required(),
                                Select::make('suffix')
                                    ->options(Suffix::groupedOptions())
                                    ->selectablePlaceholder(true)
                                    ->searchable(true),
                                Select::make('gender')
                                    ->options(Gender::options())
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                        Tab::make('Additional Information')
                            ->schema([
                                DateTimePicker::make('birth_date')
                                    ->label('Birth Date')
                                    ->time(false),
                                Select::make('house_team')
                                    ->label('House Team')
                                    ->visible(config('system-features.house_team.enabled'))
                                    ->native(false)
                                    ->selectablePlaceholder(true)
                                    ->options(config('system-features.house_team.options')),
                                TextInput::make('contact')
                                    ->label('Contact Number'),
                                TextInput::make('address')
                                    ->label('Address'),
                                TextInput::make('email_address')
                                    ->label('Email Address')
                                    ->email(true),
                            ]),
                    ]),
            ]);
    }
}
