<?php

namespace App\Filament\App\Resources\Office\Students\Tables;

use App\Enums\Gender;
use App\Enums\Status;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('facehash_avatar_url')
                    ->label('Image')
                    ->circular(),
                TextColumn::make('student_lrn')
                    ->visible(config('system-features.student_lrn'))
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('suffix')
                    ->searchable(),
                TextColumn::make('gender')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Gender::options()[$state] ?? ''),
                ToggleColumn::make('active')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('active')
                    ->selectablePlaceholder(true)
                    ->native(false)
                    ->options(Status::class),
                SelectFilter::make('gender')
                    ->selectablePlaceholder(true)
                    ->native(false)
                    ->options(Gender::class)
            ])
            ->recordActions([
                EditAction::make()
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('success')
                    ->outlined(true)
                    ->button(),
            ])
            ->defaultSort('last_name')
            ->defaultPaginationPageOption(25)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(true);
    }
}
