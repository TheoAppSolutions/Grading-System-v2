<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Pages;

use App\Filament\Admin\Resources\School\SchoolYears\SchoolYearResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\SchoolYearInfo;
use App\Models\SchoolYearCardTemplate;
use App\Models\Config\CardTemplate;
use App\Models\Config\GradeLevel;
use App\Models\Homeroom;
use Filament\Actions\Action;
use Override;

class CreateSchoolYear extends CreateRecord
{
    protected static string $resource = SchoolYearResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['active'] = 0;
        return parent::mutateFormDataBeforeCreate($data);
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        for ($count = 1; $count <= $record->attendance_entries; $count++) {
            SchoolYearInfo::create([
                'school_year_id' => $record->id,
                'coverage' => $count,
                'days' => 0,
            ]);
        }

        $cardTemplates = CardTemplate::all()->pluck('category')->toArray();
        foreach ($cardTemplates as $category) {
            SchoolYearCardTemplate::create([
                'school_year_id' => $record->id,
                'category' => $category,
            ]);
        }

        $gradeLevel = GradeLevel::all();
        foreach ($gradeLevel as $level) {
            Homeroom::create([
                'school_year_id' => $record->id,
                'grade_level_id' => $level->id,
            ]);
        }
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')->hidden();
    }
}
