<?php

namespace App\Filament\App\Resources\Office\SubjectTeacher\Pages;

use App\Filament\App\Resources\Office\SubjectTeacher\SubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Helpers\MyTools;
use App\Models\SchoolYear;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Models\Subject;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    public array $pendingChanges = [];

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Subject Teacher (S.Y. ' . SchoolYear::getActive()->school_year . ')';
    }

    protected function getHeaderActions(): array
    {
        $schoolYear = MyTools::getSchoolYear();
        return [
            Action::make('import')
                ->label('Import Records')
                ->color('success'),
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
                                ['subject_id' => $record->id],
                                [
                                    'user_id' => $changes['teacher'] ?: null,
                                    'school_year_id' => $schoolYear->id,
                                ]
                            );
                        }

                        if (isset($changes['units'])) {
                            $record->subjectUnit()->updateOrCreate(
                                ['subject_id' => $record->id],
                                [
                                    'units' => $changes['units'],
                                    'school_year_id' => $schoolYear->id,
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
}
