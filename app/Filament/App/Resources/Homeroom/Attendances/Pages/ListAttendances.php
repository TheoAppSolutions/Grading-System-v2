<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Pages;

use App\Filament\App\Resources\Homeroom\Attendances\AttendanceResource;
use App\Models\SchoolYear;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Attendance (S.Y. ' . SchoolYear::getActive()->school_year . ')';
    }
}
