<?php

namespace App\Filament\Admin\Resources\Sections\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('grade_level')
                    ->numeric(),
                TextEntry::make('section'),
                TextEntry::make('school_year'),
            ]);
    }
}
