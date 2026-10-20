<?php

namespace App\Filament\App\Resources\Office\CommentCriterias\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommentCriteriaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('category.label')
                    ->label('Category'),
                TextEntry::make('code'),
                TextEntry::make('type')
                    ->formatStateUsing(fn (int|string $state): string => match ((int) $state) {
                        1 => 'Midquarter',
                        2 => 'Quarter',
                        default => 'Unknown',
                    }),
                TextEntry::make('description')
                    ->columnSpanFull(),
                IconEntry::make('active')
                    ->boolean(),
            ]);
    }
}
