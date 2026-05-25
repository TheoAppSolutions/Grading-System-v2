<?php

namespace App\Filament\App\Resources\Office\Subjects;

use App\Filament\App\Resources\Office\Subjects\Pages\CreateSubject;
use App\Filament\App\Resources\Office\Subjects\Pages\EditSubject;
use App\Filament\App\Resources\Office\Subjects\Pages\ListSubjects;
use App\Filament\App\Resources\Office\Subjects\Schemas\SubjectForm;
use App\Filament\App\Resources\Office\Subjects\Tables\SubjectsTable;
use App\Models\Subject;
use BackedEnum;
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

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

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
