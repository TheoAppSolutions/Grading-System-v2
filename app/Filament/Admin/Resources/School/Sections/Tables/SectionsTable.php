<?php

namespace App\Filament\Admin\Resources\School\Sections\Tables;

use App\Models\SchoolYear;
use App\Models\Section;
use Filament\Actions\EditAction;
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
                Section::query()
                    ->when(
                        $schoolYear,
                        fn ($query) => $query->where('school_year_id', $schoolYear->id),
                        fn ($query) => $query->whereRaw('1 = 0'),
                    )
            )
            ->columns([
                TextColumn::make('label')
                    ->label('Section / Strand')
                    ->searchable(),
                TextColumn::make('gradeLevel.label')
                    ->label('Grade Level'),
            ])
            ->recordActions([
                EditAction::make()
                    ->button(),
            ]);
    }
}
