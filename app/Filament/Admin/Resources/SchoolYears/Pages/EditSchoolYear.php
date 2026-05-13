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
        SchoolYearInfo::updateSchoolYearInfo($data);
        unset($data['attendance_entry']);
        $data['active'] = '0';
        return parent::mutateFormDataBeforeSave($data);
    }
}
