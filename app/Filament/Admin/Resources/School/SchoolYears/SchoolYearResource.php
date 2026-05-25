<?php

namespace App\Filament\Admin\Resources\School\SchoolYears;

use App\Filament\Admin\Resources\School\SchoolYears\Pages\CreateSchoolYear;
use App\Filament\Admin\Resources\School\SchoolYears\Pages\EditSchoolYear;
use App\Filament\Admin\Resources\School\SchoolYears\Pages\ListSchoolYears;
use App\Filament\Admin\Resources\School\SchoolYears\Schemas\SchoolYearForm;
use App\Filament\Admin\Resources\School\SchoolYears\Tables\SchoolYearsTable;
use App\Filament\Admin\Resources\School\SchoolYears\RelationManagers;
use App\Models\SchoolYear;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SchoolYearResource extends Resource
{
    protected static ?string $model = SchoolYear::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $recordTitleAttribute = 'school_year';

    protected static string|UnitEnum|null $navigationGroup = 'School Settings';

    protected static ?string $navigationLabel = "School Year";

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'school-year';

    public static function form(Schema $schema): Schema
    {
        return SchoolYearForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolYearsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SchoolYearInfoRelationManager::class,
            RelationManagers\SchoolYearCardTemplateRelationManager::class,
            RelationManagers\HomeroomRelationManager::class,
            RelationManagers\SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchoolYears::route('/'),
            'create' => CreateSchoolYear::route('/create'),
            'edit' => EditSchoolYear::route('/{record}/edit'),
        ];
    }
}
