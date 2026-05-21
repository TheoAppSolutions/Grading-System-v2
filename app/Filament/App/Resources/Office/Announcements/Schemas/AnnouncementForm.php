<?php

namespace App\Filament\App\Resources\Office\Announcements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title'),
                TextInput::make('description')
                    ->required(),
                TextInput::make('from_user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('to_user_id'),
                TextInput::make('attached_files'),
                DatePicker::make('date_start'),
                DatePicker::make('date_end'),
            ]);
    }
}
