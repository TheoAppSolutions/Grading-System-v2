<?php

namespace App\Filament\App\Resources\Office\Reports;

use App\Filament\App\Resources\Office\Reports\Pages\ListReports;
use App\Filament\App\Resources\Office\Reports\Tables\ReportsTable;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Support\ReportAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ReportsResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Report Cards';

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return ReportAccess::canAccessResource(
            Auth::user(),
            SchoolYear::getActive()?->id,
        );
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return SchoolYear::getActive() !== null && static::canAccess();
    }

    public static function table(Table $table): Table
    {
        return ReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
        ];
    }
}
