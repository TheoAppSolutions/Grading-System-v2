<?php

namespace App\Filament\Admin\Resources\System\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Config\Role;
use Filament\Support\Icons\Heroicon;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('honorifics'),
                TextColumn::make('fullName')
                    ->label('Name')
                    ->getStateUsing(fn($record) => "{$record->first_name} {$record->last_name}"),
                TextColumn::make('userRoles.role.label')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->color(
                        fn(string $state): string =>
                        Role::all()->firstWhere('label', $state)?->color_template ?? 'gray'
                    ),
                ToggleColumn::make('active')
                    ->disabled(fn($record) => Auth::id() === $record->id),
            ])
            ->filters([
                //
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('active', 'desc')
                    ->orderBy('name', 'asc');
            })
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->label('View')
                    ->icon(Heroicon::Eye),
            ]);
    }
}
