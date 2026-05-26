<?php

namespace App\Filament\Admin\Resources\School\Subjects;

use App\Filament\Admin\Resources\School\Subjects\Pages\CreateSubject;
use App\Filament\Admin\Resources\School\Subjects\Pages\EditSubject;
use App\Filament\Admin\Resources\School\Subjects\Pages\ListSubjects;
use App\Filament\Admin\Resources\School\Subjects\Schemas\SubjectForm;
use App\Filament\Admin\Resources\School\Subjects\Tables\SubjectsTable;
use App\Filament\Admin\Resources\School\Subjects\RelationManagers;
use App\Models\Subject;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'subject_title';

    protected static string|UnitEnum|null $navigationGroup = 'School Settings';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SubjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubjectExcludedRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubjects::route('/'),
            'create' => CreateSubject::route('/create'),
            'edit' => EditSubject::route('/{record}/edit'),
        ];
    }
}
