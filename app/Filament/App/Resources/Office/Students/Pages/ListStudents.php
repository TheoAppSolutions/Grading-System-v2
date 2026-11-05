<?php

namespace App\Filament\App\Resources\Office\Students\Pages;

use App\Filament\App\Resources\Office\Students\StudentResource;
use App\Models\SchoolYear;
use App\Support\StudentImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importStudents')
                ->label('Import students')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->schema([
                    Wizard::make([
                        Step::make('Student data')
                            ->schema([
                                Textarea::make('import_code')
                                    ->label('Paste student import code')
                                    ->helperText('One student per row: ID,Last,First,Middle,Suffix,Gender,Grade,Contact,Address,Email. Separate rows with |.')
                                    ->rows(8)
                                    ->required(),
                            ]),
                        Step::make('Review')
                            ->schema([
                                Placeholder::make('import_preview')
                                    ->label('Import preview')
                                    ->content(function (Get $get): HtmlString {
                                        $code = (string) ($get('import_code') ?? '');
                                        $preview = app(StudentImport::class)->preview($code);

                                        return new HtmlString(view('filament.app.pages.student-import-preview', $preview)->render());
                                    }),
                            ]),
                    ])
                        ->skippable(false),
                ])
                ->modalSubmitActionLabel('Import students')
                ->action(function (array $data): void {
                    $schoolYear = SchoolYear::getActive();
                    abort_unless($schoolYear && auth()->user()?->isOffice() === true, 403);

                    $count = app(StudentImport::class)->import($data['import_code'], $schoolYear);
                    Notification::make()
                        ->title("{$count} students imported and enrolled")
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => auth()->user()?->isOffice() === true && SchoolYear::getActive() !== null),
        ];
    }
}
