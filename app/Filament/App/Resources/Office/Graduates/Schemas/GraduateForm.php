<?php

namespace App\Filament\App\Resources\Office\Graduates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GraduateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                TextInput::make('graduated_at')
                    ->required(),
            ]);
    }
}
