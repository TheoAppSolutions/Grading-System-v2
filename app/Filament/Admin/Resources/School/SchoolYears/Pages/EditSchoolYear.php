<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Pages;

use App\Filament\Admin\Resources\School\SchoolYears\SchoolYearResource;
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
            parent::getCancelFormAction()
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->record;
        $newCount = (int) $data['attendance_entries'];
        $existingCount = SchoolYearInfo::where('school_year_id', $record->id)->count();

        if ($newCount < $existingCount) {
            SchoolYearInfo::where('school_year_id', $record->id)
                ->where('coverage', '>', $newCount)
                ->delete();
        } elseif ($newCount > $existingCount) {
            for ($coverage = $existingCount + 1; $coverage <= $newCount; $coverage++) {
                SchoolYearInfo::create([
                    'school_year_id' => $record->id,
                    'coverage'       => $coverage,
                    'days'           => 0,
                ]);
            }
        }
        // if $newCount === $existingCount, nothing to do

        return parent::mutateFormDataBeforeSave($data);
    }

    #[Override]
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->hidden();
    }

    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
    }
}
