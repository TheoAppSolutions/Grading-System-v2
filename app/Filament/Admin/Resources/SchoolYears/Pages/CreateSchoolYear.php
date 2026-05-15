<?php

namespace App\Filament\Admin\Resources\SchoolYears\Pages;

use App\Filament\Admin\Resources\SchoolYears\SchoolYearResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\SchoolYearInfo;
use App\Models\SchoolYearCardTemplate;
use App\Models\Sushi\CardTemplate;
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
        for ($month = 1; $month <= $record->attendance_entries; $month++) {
            SchoolYearInfo::create([
                'school_year_id' => $record->id,
                'month' => $month,
                'days' => 0,
            ]);
        }

        $cardTemplates = CardTemplate::all()->pluck('category')->toArray();
        foreach ($cardTemplates as $i => $category) {
            SchoolYearCardTemplate::create([
                'school_year_id' => $record->id,
                'card_template_id' => $i + 1,
                'category' => $category,
            ]);
        }
    }
}
