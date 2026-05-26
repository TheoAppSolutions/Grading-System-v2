<?php

namespace App\Filament\App\Resources\Office\SubjectTeacher;

use App\Filament\App\Resources\Office\SubjectTeacher\Pages\CreateSubject;
use App\Filament\App\Resources\Office\SubjectTeacher\Pages\EditSubject;
use App\Filament\App\Resources\Office\SubjectTeacher\Pages\ListSubjects;
use App\Filament\App\Resources\Office\SubjectTeacher\Schemas\SubjectForm;
use App\Filament\App\Resources\Office\SubjectTeacher\Tables\SubjectsTable;
use App\Filament\App\Resources\Office\SubjectTeacher\RelationManagers;
use App\Models\Subject;
use App\Models\SubjectUser;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = SubjectUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?string $recordTitleAttribute = 'subject_title';

    protected static ?string $slug = 'subject_teacher';

    protected static ?int $navigationSort = 1;

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
            //
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
