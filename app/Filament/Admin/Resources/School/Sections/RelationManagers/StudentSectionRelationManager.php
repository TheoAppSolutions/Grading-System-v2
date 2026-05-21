<?php

namespace App\Filament\Admin\Resources\School\Sections\RelationManagers;

use App\Filament\Admin\Resources\School\Sections\SectionResource;
use Filament\Actions\AssociateAction;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class StudentSectionRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollment';

    public function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->headerActions([
                AssociateAction::make(),
            ]);
    }
}
