<?php

namespace App\Filament\App\Resources\Teacher\Grades;

use App\Filament\App\Resources\Teacher\Grades\Pages\CreateGrade;
use App\Filament\App\Resources\Teacher\Grades\Pages\EditGrade;
use App\Filament\App\Resources\Teacher\Grades\Pages\ListGrades;
use App\Filament\App\Resources\Teacher\Grades\Schemas\GradeForm;
use App\Filament\App\Resources\Teacher\Grades\Tables\GradesTable;
use App\Models\Grade;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Teacher Menu';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return GradeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGrades::route('/'),
            'create' => CreateGrade::route('/create'),
            'edit' => EditGrade::route('/{record}/edit'),
        ];
    }
}
