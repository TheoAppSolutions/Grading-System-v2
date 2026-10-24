<?php

namespace App\Filament\App\Resources\Office\Students\RelationManagers;

use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $title = 'Enrollment Records';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['schoolYear', 'gradeLevel', 'section', 'student']))
            ->columns([
                TextColumn::make('schoolYear.label')
                    ->label('School Year')
                    ->placeholder('—'),
                TextColumn::make('gradeLevel.label')
                    ->label('Grade Level')
                    ->placeholder('—'),
                TextColumn::make('section.label')
                    ->label('Section/Strand')
                    ->placeholder('—')
                    ->visible(config('system-features.sections')),
            ])
            ->emptyStateHeading('No Enrollment Records Found.')
            ->emptyStateDescription('This student does not have an enrollment record yet.')
            ->recordActions([
                Action::make('show_report')
                    ->label('Report Card')
                    ->button()
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->color('success')
                    ->url(fn ($record) => route('report.quarter', [$record->school_year_id, $record->student_id]))
                    ->outlined(true)
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => filled($record->school_year_id) && filled($record->student_id)),
                Action::make('show_award_report')
                    ->label('Award Report')
                    ->button()
                    ->icon(Heroicon::OutlinedTrophy)
                    ->size(Size::ExtraSmall)
                    ->color('warning')
                    ->url(fn ($record) => route('report.award', [$record->school_year_id, $record->student_id]))
                    ->outlined(true)
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => filled($record->school_year_id) && filled($record->student_id)),
            ])
            ->headerActions([
                Action::make('award_summary')
                    ->label('Awards Summary')
                    ->button()
                    ->color('warning')
                    ->icon(Heroicon::OutlinedTrophy)
                    ->size(Size::ExtraSmall)
                    ->url(fn () => route('report.award.student.summary', [$this->ownerRecord->id]))
                    ->outlined(true)
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => $this->ownerRecord->enrollments()->exists()),
                Action::make('tor')
                    ->label('Transcript of Records')
                    ->button()
                    ->color(Color::Amber)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::ExtraSmall)
                    ->url(fn () => route('report.tor', [$this->ownerRecord->id]))
                    ->outlined(true)
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('school_year_id', 'desc');
    }
}
