<?php

namespace App\Filament\Admin\Resources\School\Enrollments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('grade_level')
                    ->badge(),

                TextColumn::make('enrolled_at')
                    ->date(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                    // Built-in: delete selected
                    DeleteBulkAction::make(),

                    // Custom: assign to class
                    BulkAction::make('assignToClass')
                        ->label('Assign to class')
                        ->icon('heroicon-o-academic-cap')
                        ->action(function (Collection $records): void {
                            // $records = all checked students
                            foreach ($records as $student) {
                                $student->update(['class_id' => 42]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Custom: export selected
                    BulkAction::make('export')
                        ->label('Export to CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            // return download response
                        }),
                ]),
            ]);
    }
}
