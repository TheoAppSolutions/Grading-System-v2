<?php

namespace App\Filament\App\Resources\Office\Enrollments;

use App\Filament\App\Resources\Office\Enrollments\Pages\CreateEnrollment;
use App\Filament\App\Resources\Office\Enrollments\Pages\EditEnrollment;
use App\Filament\App\Resources\Office\Enrollments\Pages\ListEnrollments;
use App\Filament\App\Resources\Office\Enrollments\Schemas\EnrollmentForm;
use App\Filament\App\Resources\Office\Enrollments\Tables\EnrollmentsTable;
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

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'enrollment';

    public static function form(Schema $schema): Schema
    {
        return EnrollmentForm::configure($schema);
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
            'edit' => EditEnrollment::route('/{record}/edit'),
        ];
    }
}
