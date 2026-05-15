<?php

namespace App\Filament\Admin\Resources\SchoolYears\Pages;

use App\Filament\Admin\Resources\SchoolYears\SchoolYearResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\SchoolYearInfo;
use Filament\Actions\Action;
use Override;
use Filament\Pages\Enums\SubNavigationPosition;

class EditSchoolYear extends EditRecord
{
    protected static string $resource = SchoolYearResource::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            parent::getSaveFormAction(),
            parent::getCancelFormAction()
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
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

    #[Override]
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->hidden();
    }

    #[Override]
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->hidden();
    }
}
