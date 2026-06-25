<?php

namespace App\Filament\App\Resources\Office\Students\RelationManagers;

use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;

class EnrollmentRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $title = 'Enrollment Records';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('schoolYear.label')
                    ->label('School Year'),
                TextColumn::make('gradeLevel.label')
                    ->label('Grade Level'),
                TextColumn::make('section.label')
                    ->label('Section/Strand')
                    ->visible(config('system-features.sections'))
            ])
            ->emptyStateHeading('No Enrollment Records Found.')
            ->recordActions([
                Action::make('show_reports')
                    ->label('Report Card')
                    ->button()
                    ->color(Color::Sky)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->outlined(true),
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        $pdf = Pdf::loadView('reports.templates.upper_school.secondary_T1-T3', [
                            'grades' => $record,
                            'quarter' => 4,
                            'student' => $record->load('student')
                        ])->setPaper('a4', 'portrait');

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            "Report-{$record->id}.pdf"
                        );
                    }),
                Action::make('show_awards')
                    ->label('Awards Card')
                    ->button()
                    ->color(Color::Indigo)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->outlined(true)
            ])
            ->headerActions([
                Action::make('tor')
                    ->label('Transcript of Records')
                    ->button()
                    ->color(Color::Emerald)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->outlined(true)
            ]);
    }
}
