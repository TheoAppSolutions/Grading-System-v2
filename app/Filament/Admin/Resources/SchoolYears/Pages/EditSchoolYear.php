<?php

namespace App\Filament\Admin\Resources\SchoolYears\Pages;

use App\Filament\Admin\Resources\SchoolYears\SchoolYearResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\SchoolYearInfo;
use Override;

class EditSchoolYear extends EditRecord
{
    protected static string $resource = SchoolYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->record;

        if ($record->isDirty('attendance_entries')) {
            SchoolYearInfo::where('school_year_id', $record->id)->delete();
            for ($month = 1; $month <= $record->attendance_entries; $month++) {
                SchoolYearInfo::create([
                    'school_year_id' => $record->id,
                    'month' => $month,
                    'days' => 0,
                ]);
            }
        }

        return parent::mutateFormDataBeforeSave($data);
    }
}
