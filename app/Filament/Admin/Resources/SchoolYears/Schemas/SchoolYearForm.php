<?php

namespace App\Filament\Admin\Resources\SchoolYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SchoolYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('active')
                    ->required(),
                TextInput::make('school_year')
                    ->required(),
                TextInput::make('school_year_start')
                    ->numeric(),
                TextInput::make('school_year_end')
                    ->numeric(),
                Textarea::make('school_days')
                    ->columnSpanFull(),
                TextInput::make('days_settings')
                    ->required()
                    ->default('4'),
                TextInput::make('card_template')
                    ->required()
                    ->default(''),
            ]);
    }
}
