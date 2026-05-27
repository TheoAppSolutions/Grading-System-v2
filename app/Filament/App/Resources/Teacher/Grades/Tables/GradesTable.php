<?php

namespace App\Filament\App\Resources\Teacher\Grades\Tables;

use App\Models\Grade;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Student::query()->select('students.*'))
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn($record) => $record->last_name . ', ' . $record->first_name)
                    ->sortable(['last_name', 'first_name']),

                TextInputColumn::make('midquarter')
                    ->label('MidQuarter')
                    ->type('number')
                    ->rules(['numeric', 'min:0', 'max:100'])
                    ->visible(fn($livewire) => $livewire->quarterType === 1)
                    ->getStateUsing(
                        fn($record, $livewire) =>
                        $livewire->pendingChanges[$record->id]['midquarter'] ?? $record->midquarter
                    )
                    ->updateStateUsing(
                        fn($record, $state, $livewire) =>
                        $livewire->pendingChanges[$record->id]['midquarter'] = $state
                    ),

                TextInputColumn::make('final')
                    ->label('Final')
                    ->type('number')
                    ->rules(['numeric', 'min:0', 'max:100'])
                    ->visible(fn($livewire) => $livewire->quarterType === 2)
                    ->getStateUsing(
                        fn($record, $livewire) =>
                        $livewire->pendingChanges[$record->id]['final'] ?? $record->final
                    )
                    ->updateStateUsing(
                        fn($record, $state, $livewire) =>
                        $livewire->pendingChanges[$record->id]['final'] = $state
                    ),

                TextInputColumn::make('effort')
                    ->label('Effort')
                    ->type('number')
                    ->rules(['numeric', 'min:0', 'max:5'])
                    ->visible(fn($livewire) => $livewire->quarterType === 2)
                    ->getStateUsing(
                        fn($record, $livewire) =>
                        $livewire->pendingChanges[$record->id]['effort'] ?? $record->effort
                    )
                    ->updateStateUsing(
                        fn($record, $state, $livewire) =>
                        $livewire->pendingChanges[$record->id]['effort'] = $state
                    ),

                TextInputColumn::make('deportment')
                    ->label('Deportment')
                    ->type('number')
                    ->rules(['numeric', 'min:0', 'max:100'])
                    ->visible(fn($livewire) => $livewire->quarterType === 2)
                    ->getStateUsing(
                        fn($record, $livewire) =>
                        $livewire->pendingChanges[$record->id]['deportment'] ?? $record->deportment
                    )
                    ->updateStateUsing(
                        fn($record, $state, $livewire) =>
                        $livewire->pendingChanges[$record->id]['deportment'] = $state
                    ),
            ])
            ->headerActions([
                Action::make('apply_midquarter')
                    ->label('MidQuarter')
                    ->color(fn($livewire) => $livewire->quarterType == 1 ? 'success' : 'transparent')
                    ->action(function ($livewire) {
                        $livewire->quarterType = 1;
                    }),

                Action::make('apply_quarter')
                    ->label('Quarter')
                    ->color(fn($livewire) => $livewire->quarterType == 2 ? 'success' : 'transparent')
                    ->action(function ($livewire) {
                        $livewire->quarterType = 2;
                    }),
            ], position: HeaderActionsPosition::Bottom)
            ->recordUrl('');
    }
}
