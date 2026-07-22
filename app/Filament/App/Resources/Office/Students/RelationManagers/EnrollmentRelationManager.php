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
                Action::make('show_report')
                    ->label('Report Card')
                    ->button()
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->color('success')
                    ->url(fn($record) => route('report.quarter', [$record->school_year_id, $record->student_id]))
                    ->outlined(true)
                    ->openUrlInNewTab(),
                Action::make('show_awards')
                    ->label('Awards Card')
                    ->button()
                    ->color(Color::Indigo)
                    ->url(fn($record) => route('report.award', [$record->school_year_id, $record->student_id]))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->outlined(true)
                    ->openUrlInNewTab()
            ])
            ->headerActions([
                Action::make('tor')
                    ->label('Transcript of Records')
                    ->button()
                    ->color(Color::Amber)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->url(fn() => route('report.tor', [$this->ownerRecord->id]))
                    ->outlined(true)
                    ->openUrlInNewTab()
            ]);
    }
}
