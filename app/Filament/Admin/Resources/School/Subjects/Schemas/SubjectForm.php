<?php

namespace App\Filament\Admin\Resources\School\Subjects\Schemas;

use App\Models\Config\GradeLevel;
use App\Models\Config\SubjectCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_level_id')
                    ->required()
                    ->searchable()
                    ->label('Grade Level')
                    ->options(GradeLevel::all()->sortBy('sort_order')->pluck('label', 'id'))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('subject_category_id', null)),
                Select::make('subject_category_id')
                    ->required()
                    ->searchable()
                    ->label('Subject Category')
                    ->options(function (callable $get) {
                        $gradeLevelId = $get('grade_level_id');

                        if (! $gradeLevelId) {
                            return SubjectCategory::all()->pluck('label', 'id');
                        }

                        $gradeLevel = GradeLevel::find($gradeLevelId);

                        if (! $gradeLevel) {
                            return [];
                        }

                        return SubjectCategory::query()
                            ->where(function ($query) use ($gradeLevel) {
                                $query->where('category', $gradeLevel->category);

                                if ($gradeLevel->category === 'primary' || $gradeLevel->category === 'secondary') {
                                    $query->orWhere('category', 'general');
                                }
                            })
                            ->pluck('label', 'id');
                    })
                    ->live()
                    ->visible(config('app.system_features.sections'))
                    ->disabled(fn (callable $get) => ! $get('grade_level_id')),
                TextInput::make('subject_title')
                    ->label('Subject Title')
                    ->columnSpanFull()
                    ->required()
                    ->default(''),
            ]);
    }
}
