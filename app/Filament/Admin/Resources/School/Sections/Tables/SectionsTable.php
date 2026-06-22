<?php

namespace App\Filament\Admin\Resources\School\Sections\Tables;

use App\Models\SchoolYear;
use App\Models\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        $schoolYear = SchoolYear::getActive();

        return $table
            ->query(
                Section::where('school_year_id', $schoolYear->id)
            )
            ->columns([
                TextColumn::make('section')
                    ->label('Section / Strand')
                    ->searchable(),
                TextColumn::make('gradeLevel.label'),
            ])
            ->recordActions([]);
    }
}
