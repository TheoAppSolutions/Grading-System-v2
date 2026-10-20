<?php

namespace App\Filament\App\Resources\Homeroom\Attendances;

use App\Filament\App\Resources\Homeroom\Attendances\Pages\ListAttendances;
use App\Filament\App\Resources\Homeroom\Attendances\Schemas\AttendanceForm;
use App\Filament\App\Resources\Homeroom\Attendances\Tables\AttendancesTable;
use App\Models\Attendance;
use App\Models\Homeroom;
use App\Models\SchoolYear;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Homeroom Menu';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $schoolYearId = SchoolYear::getActive()?->id;

        if (! $schoolYearId || ! Auth::id()) {
            return false;
        }

        return Homeroom::query()
            ->where('school_year_id', $schoolYearId)
            ->where(function (Builder $query): void {
                $query->where('user_id', Auth::id())
                    ->orWhere('user2_id', Auth::id());
            })
            ->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
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
            'index' => ListAttendances::route('/'),
        ];
    }
}
