<?php

namespace App\Filament\Admin\Resources\School\Enrollments;

use App\Filament\Admin\Resources\School\Enrollments\Pages\CreateEnrollment;
use App\Filament\Admin\Resources\School\Enrollments\Pages\EditEnrollment;
use App\Filament\Admin\Resources\School\Enrollments\Pages\ListEnrollments;
use App\Filament\Admin\Resources\School\Enrollments\Pages\ViewEnrollment;
use App\Filament\Admin\Resources\School\Enrollments\Schemas\EnrollmentForm;
use App\Filament\Admin\Resources\School\Enrollments\Schemas\EnrollmentInfolist;
use App\Filament\Admin\Resources\School\Enrollments\Tables\EnrollmentsTable;
use App\Models\Enrollment;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string | UnitEnum | null $navigationGroup = 'School Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'enrollment';

    public static function form(Schema $schema): Schema
    {
        return EnrollmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EnrollmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnrollmentsTable::configure($table);
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
            'index' => ListEnrollments::route('/'),
            'create' => CreateEnrollment::route('/create'),
            'view' => ViewEnrollment::route('/{record}'),
            'edit' => EditEnrollment::route('/{record}/edit'),
        ];
    }
}
