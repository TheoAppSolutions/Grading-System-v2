<?php

namespace App\Filament\App\Resources\Graduates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GraduateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.name')
                    ->label('Student'),
                TextEntry::make('graduated_at'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
