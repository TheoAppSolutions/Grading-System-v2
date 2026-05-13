<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                    ->placeholder('-'),
                TextEntry::make('color_template')
                    ->placeholder('-'),
            ]);
    }
}
