<?php

namespace App\Filament\App\Resources\Office\Reports\Pages;

use App\Filament\App\Resources\Office\Reports\ReportsResource;
use App\Models\SchoolYear;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListReports extends ListRecords
{
    protected static string $resource = ReportsResource::class;

    public function getTitle(): string
    {
        $schoolYear = SchoolYear::getActive();

        return 'Reports'.($schoolYear ? " (S.Y. {$schoolYear->label})" : '');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('report_card')
                ->label('Report Card')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('success')
                ->button()
                ->outlined()
                ->openUrlInNewTab()
                ->url(function (): string {
                    $schoolYear = SchoolYear::getActive();

                    if (! $schoolYear) {
                        return '#';
                    }

                    $parameters = ['school_year_id' => $schoolYear->id];
                    $gradeLevelId = data_get($this->tableFilters, 'grade_level_id.value');

                    if (filled($gradeLevelId)) {
                        $parameters['grade_level_id'] = $gradeLevelId;
                    }

                    return route('report.quarter.batch', $parameters);
                }),
        ];
    }
}
