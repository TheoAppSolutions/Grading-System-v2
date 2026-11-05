<?php

namespace App\Filament\App\Resources\Office\SubjectTeacher\Pages;

use App\Filament\App\Resources\Office\SubjectTeacher\SubjectResource;
use Filament\Resources\Pages\ListRecords;
use App\Models\SchoolYear;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Override;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    public array $pendingChanges = [];

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Subject Teacher (S.Y. '.(SchoolYear::getActive()?->label ?? 'Not configured').')';
    }

    protected function getHeaderActions(): array
    {
        $schoolYear = SchoolYear::getActive();

        if (! $schoolYear) {
            return [];
        }

        return [
            Action::make('importSubjectLoad')
                ->label('Import Previous Subject Load')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->modalHeading('Choose a previous subject load')
                ->modalDescription('Select the school year you want to import. You will review a separate confirmation before anything is changed.')
                ->modalSubmitActionLabel('Import subject load')
                ->schema([
                    Select::make('school_year_id')
                        ->label('Previous school year')
                        ->options(fn (): array => SchoolYear::query()
                            ->where('active', '!=', 1)
                            ->orderByDesc('id')
                            ->pluck('label', 'id')
                            ->all())
                        ->required()
                        ->searchable()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    $this->replaceMountedAction(
                        'confirmSubjectLoadImport',
                        arguments: ['school_year_id' => $data['school_year_id']],
                    );
                })
                ->visible(fn (): bool => SchoolYear::query()->where('active', '!=', 1)->exists()),

            Action::make('saveAll')
                ->label('Save All Changes')
                ->action(function ($livewire) use ($schoolYear) {
                    if (empty($livewire->pendingChanges)) {
                        Notification::make()
                            ->title('No changes to save')
                            ->warning()
                            ->send();
                        return;
                    }

                    foreach ($livewire->pendingChanges as $subjectId => $changes) {
                        $record = Subject::find($subjectId);
                        if (!$record) continue;

                        if (isset($changes['teacher'])) {
                            $record->subjectUser()->updateOrCreate(
                                [
                                    'subject_id' => $record->id,
                                    'school_year_id' => $schoolYear->id,
                                ],
                                [
                                    'user_id' => $changes['teacher'] ?: null,
                                ]
                            );
                        }

                        if (isset($changes['units'])) {
                            $record->subjectUnit()->updateOrCreate(
                                [
                                    'subject_id' => $record->id,
                                    'school_year_id' => $schoolYear->id,
                                ],
                                [
                                    'units' => $changes['units'],
                                ]
                            );
                        }
                    }

                    $livewire->pendingChanges = [];

                    Notification::make()
                        ->title('All changes saved successfully')
                        ->success()
                        ->send();

                    $this->dispatch('refresh-sidebar');
                }),
        ];
    }

    protected function confirmSubjectLoadImportAction(): Action
    {
        return Action::make('confirmSubjectLoadImport')
            ->requiresConfirmation()
            ->modalHeading('Overwrite subject load records?')
            ->modalDescription('Are you sure you want to overwrite the current teacher and unit assignments with data from the selected school year?')
            ->modalSubmitActionLabel('Yes, overwrite records')
            ->action(function (array $arguments): void {
                $schoolYear = SchoolYear::getActive();

                if (! $schoolYear) {
                    return;
                }

                $sourceSchoolYear = SchoolYear::query()
                    ->whereKey($arguments['school_year_id'])
                    ->where('active', '!=', 1)
                    ->firstOrFail();

                $activeTeacherIds = User::query()
                    ->where('active', true)
                    ->pluck('id')
                    ->all();

                $importedCount = DB::transaction(function () use ($schoolYear, $sourceSchoolYear, $activeTeacherIds): int {
                    $importedCount = 0;

                    $subjects = Subject::query()
                        ->where('active', true)
                        ->with([
                            'subjectUser' => fn ($query) => $query->where('school_year_id', $sourceSchoolYear->id),
                            'subjectUnit' => fn ($query) => $query->where('school_year_id', $sourceSchoolYear->id),
                        ])
                        ->get();

                    foreach ($subjects as $subject) {
                        $sourceAssignment = $subject->subjectUser->first();
                        $sourceUnits = $subject->subjectUnit->first();

                        if (! $sourceAssignment && ! $sourceUnits) {
                            continue;
                        }

                        if ($sourceAssignment) {
                            $subject->subjectUser()->updateOrCreate(
                                ['school_year_id' => $schoolYear->id],
                                [
                                    'user_id' => in_array($sourceAssignment->user_id, $activeTeacherIds) ? $sourceAssignment->user_id : null,
                                    'user2_id' => in_array($sourceAssignment->user2_id, $activeTeacherIds) ? $sourceAssignment->user2_id : null,
                                    'semester' => $sourceAssignment->semester,
                                    'category' => $sourceAssignment->category,
                                ]
                            );
                        }

                        if ($sourceUnits) {
                            $subject->subjectUnit()->updateOrCreate(
                                ['school_year_id' => $schoolYear->id],
                                ['units' => $sourceUnits->units]
                            );
                        }

                        $importedCount++;
                    }

                    return $importedCount;
                });

                $this->pendingChanges = [];
                $this->resetTable();

                Notification::make()
                    ->title("{$importedCount} subject loads imported from {$sourceSchoolYear->label}")
                    ->success()
                    ->send();
            });
    }
}
