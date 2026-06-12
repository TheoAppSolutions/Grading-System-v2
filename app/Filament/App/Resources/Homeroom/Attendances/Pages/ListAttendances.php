<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Pages;

use App\Filament\App\Resources\Homeroom\Attendances\AttendanceResource;
use App\Models\Attendance;
use App\Models\SchoolYear;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    public array $pendingChanges = [];

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Attendance (S.Y. '.SchoolYear::getActive()->school_year.')';
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveAttendance')
                ->label('Save Attendance')
                ->action(fn ($livewire) => $livewire->saveAttendance())
                ->button()
                ->color('primary'),
        ];
    }

    public function saveAttendance(): void
    {
        $schoolYear = SchoolYear::getActive();
        foreach ($this->pendingChanges as $studentId => $changes) {
            $attendance = Attendance::where(['student_id' => $studentId, 'school_year_id' => $schoolYear->id])->first();

            if (!$attendance) continue;

            $attendanceData = $attendance->attendance_data ?? [];
            $attendanceData[$studentId] ??= ['A' => 0, 'T' => 0];

            if (isset($changes['absent'])) {
                $attendanceData[$studentId]['A'] = (float) $changes['absent'];
            }
            if (isset($changes['tardy'])) {
                $attendanceData[$studentId]['T'] = (float) $changes['tardy'];
            }

            $attendance->update(['attendance_data' => $attendanceData]);
        }

        $this->pendingChanges = [];

        Notification::make()->title('Attendance saved.')->success()->send();
    }
}
