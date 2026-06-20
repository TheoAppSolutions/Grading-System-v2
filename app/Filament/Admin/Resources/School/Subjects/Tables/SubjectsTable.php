<?php

namespace App\Filament\Admin\Resources\School\Subjects\Tables;

use App\Models\Config\GradeLevel;
use App\Models\Config\SubjectCategory;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('subject_code')
                    ->label('Subject Code'),
                TextColumn::make('subject_title')
                    ->label('Title')
                    ->searchable(),
                TextColumn::make('subjectCategory.label')
                    ->label('Category')
                    ->visible(config('system-features.sections')),
            ])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->options(GradeLevel::all()->sortBy('sort_order')->pluck('label', 'id'))
                    ->default(1)
                    ->searchable(),
                // SelectFilter::make('subject_category_id')
                //     ->label('Category')
                //     ->options(SubjectCategory::all()->sortBy('sort_order')->pluck('label', 'id'))
                //     ->searchable()
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                ->button(),
            ]);
    }
}
