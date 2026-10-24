<?php

namespace App\Filament\App\Resources\Office\Reports\Tables;

use App\Enums\Gender;
use App\Models\Config\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Support\ReportAccess;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        $schoolYearId = SchoolYear::getActive()?->id;
        $user = Auth::user();
        $gradeLevelOptions = function () use ($user, $schoolYearId): array {
            $gradeLevelIds = ReportAccess::accessibleGradeLevelIds($user, $schoolYearId);
            $options = GradeLevel::query()
                ->orderBy('sort_order')
                ->pluck('label', 'id');

            return $gradeLevelIds === null
                ? $options->all()
                : $options->only($gradeLevelIds)->all();
        };
        $defaultGradeLevelId = array_key_first($gradeLevelOptions());

        return $table
            ->query(
                ReportAccess::scopeStudents(
                    Student::query()->where('active', true),
                    $user,
                    $schoolYearId,
                )->with([
                    'enrollments' => fn ($query) => $query
                        ->where('school_year_id', $schoolYearId)
                        ->with('gradeLevel'),
                ])
            )
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('gender', 'desc')
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->orderBy('middle_name')
                    ->orderBy('id');
            })
            ->columns([
                TextColumn::make('row_number')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Name')
                    ->state(fn (Student $record): string => $record->full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search): void {
                            $query
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query
                        ->orderBy('last_name', $direction)
                        ->orderBy('first_name', $direction)),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(fn (?string $state): string => Gender::options()[$state] ?? '—'),
                TextColumn::make('grade_level')
                    ->label('Grade Level')
                    ->state(fn (Student $record): string => $record->enrollments->first()?->gradeLevel?->label ?? '—')
                    ->visible(fn ($livewire): bool => blank(
                        $livewire->tableFilters['grade_level_id']['value'] ?? null,
                    )),
            ])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->searchable()
                    ->selectablePlaceholder(true)
                    ->visible(function () use ($user, $schoolYearId): bool {
                        $gradeLevelIds = ReportAccess::accessibleGradeLevelIds($user, $schoolYearId);

                        return $gradeLevelIds === null || count($gradeLevelIds) >= 2;
                    })
                    ->options($gradeLevelOptions)
                    ->default($defaultGradeLevelId)
                    ->query(function (Builder $query, array $data) use ($schoolYearId): void {
                        $query->when(
                            filled($data['value'] ?? null),
                            fn (Builder $query) => $query->whereHas(
                                'enrollments',
                                fn (Builder $enrollmentQuery) => $enrollmentQuery
                                    ->where('school_year_id', $schoolYearId)
                                    ->where('grade_level_id', $data['value']),
                            ),
                        );
                    }),
            ])
            ->recordActions([
                Action::make('report_card')
                    ->label('Report Card')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->size(Size::Small)
                    ->color('success')
                    ->button()
                    ->outlined()
                    ->openUrlInNewTab()
                    ->url(fn (Student $record): string => route(
                        'report.quarter',
                        [$record->enrollments->first()->school_year_id, $record->id],
                    )),
                Action::make('award_card')
                    ->label('Award Card')
                    ->icon(Heroicon::OutlinedTrophy)
                    ->size(Size::Small)
                    ->color('warning')
                    ->button()
                    ->outlined()
                    ->openUrlInNewTab()
                    ->url(fn (Student $record): string => route(
                        'report.award',
                        [$record->enrollments->first()->school_year_id, $record->id],
                    )),
            ]);
    }
}
