<?php

namespace App\Filament\App\Resources\Office\Reports\Pages;

use App\Filament\App\Resources\Office\Reports\ReportsResource;
use App\Models\SchoolYear;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use App\Models\Config\GradeLevel;
use App\Models\Subject;
use App\Support\ReportAccess;
use Illuminate\Support\Facades\Auth;

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
            Action::make('subjectSummary')
                ->label('Subject Summary')
                ->icon(Heroicon::OutlinedBookOpen)
                ->schema([
                    Select::make('grade_level_id')
                        ->label('Grade level')
                        ->options(fn () => $this->accessibleGradeLevels())
                        ->live()
                        ->required(),
                    Select::make('subject_id')
                        ->label('Subject')
                        ->options(fn ($get) => Subject::query()
                            ->where('grade_level_id', $get('grade_level_id'))
                            ->whereHas('subjectUser', fn ($query) => $query->where('school_year_id', SchoolYear::getActive()?->id))
                            ->orderBy('subject_code')
                            ->pluck('subject_code', 'id'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $yearId = SchoolYear::getActive()?->id;
                    abort_unless($yearId && ReportAccess::canViewGradeLevel(Auth::user(), $yearId, (int) $data['grade_level_id']), 403);
                    $this->redirectRoute('report.subject.summary', [$yearId, $data['grade_level_id'], $data['subject_id']], navigate: false);
                }),
            Action::make('subjectSummaryBatch')
                ->label('All Subject Summaries')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->schema([
                    Select::make('grade_level_id')
                        ->label('Grade level')
                        ->options(fn () => $this->accessibleGradeLevels())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $yearId = SchoolYear::getActive()?->id;
                    abort_unless($yearId && ReportAccess::canViewGradeLevel(Auth::user(), $yearId, (int) $data['grade_level_id']), 403);
                    $this->redirectRoute('report.subject.summary.batch', [$yearId, $data['grade_level_id']], navigate: false);
                }),
            Action::make('masterSummary')
                ->label('Master Summary')
                ->icon(Heroicon::OutlinedTableCells)
                ->schema([
                    Select::make('grade_level_id')
                        ->label('Grade level')
                        ->options(fn () => $this->accessibleGradeLevels())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $yearId = SchoolYear::getActive()?->id;
                    abort_unless($yearId && ReportAccess::canViewGradeLevel(Auth::user(), $yearId, (int) $data['grade_level_id']), 403);
                    $this->redirectRoute('report.master.summary', [$yearId, $data['grade_level_id']], navigate: false);
                }),
        ];
    }

    private function accessibleGradeLevels(): array
    {
        $yearId = SchoolYear::getActive()?->id;
        $ids = ReportAccess::accessibleGradeLevelIds(Auth::user(), $yearId);
        $levels = GradeLevel::query()->orderBy('sort_order')->pluck('label', 'id');

        return $ids === null ? $levels->all() : $levels->only($ids)->all();
    }
}
