<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\RelationManagers;

use App\Filament\Admin\Resources\School\Sections\SectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $relatedResource = SectionResource::class;

    protected static ?string $relationshipTitle = 'Section / Strand';

    public function table(Table $table): Table
    {
        return $table
            ->heading('')
            ->headerActions([
                CreateAction::make('New Section / Strand')
                    ->modal()
                    ->slideOver()
            ])
            ->filters([])
            ->paginated(false);
    }
}
