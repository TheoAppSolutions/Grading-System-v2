<?php

namespace App\Filament\App\Resources\Office\CommentCriterias\Tables;

use App\Models\Config\CommentCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CommentCriteriasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.label')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (int|string $state): string => match ((int) $state) {
                        1 => 'Midquarter',
                        2 => 'Quarter',
                        default => 'Unknown',
                    }),
                IconColumn::make('active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('comment_category_id')
                    ->label('Category')
                    ->options(CommentCategory::query()->pluck('label', 'id')),
                SelectFilter::make('type')
                    ->options([
                        1 => 'Midquarter',
                        2 => 'Quarter',
                    ]),
                TernaryFilter::make('active'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('code');
    }
}
