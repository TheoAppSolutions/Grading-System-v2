<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\RelationManagers;

use App\Models\Config\CardTemplate;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolYearCardTemplateRelationManager extends RelationManager
{
    protected static string $relationship = 'schoolYearCardTemplate';

    protected static ?string $relationshipTitle = 'Card Template';

    public function table(Table $table): Table
    {
        return $table
            ->heading('')
            ->columns([
                TextColumn::make('category')
                    ->label('Category'),
                SelectColumn::make('card_template_id')
                    ->options(fn($record) => CardTemplate::where('category', $record->category)->pluck('template', 'id')->toArray())
                    ->label('Template'),
            ]);
    }
}
