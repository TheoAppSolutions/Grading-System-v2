<?php

namespace App\Filament\Admin\Resources\System\Users\Tables;

use App\Enums\Status;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Config\Role;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('facehash_avatar_url')
                    ->label('Image'),
                TextColumn::make('fullName')
                    ->label('Name')
                    ->searchable()
                    ->getStateUsing(fn($record) => "{$record->honorifics} {$record->first_name} {$record->last_name}"),
                TextColumn::make('userRoles.role.label')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->color(
                        fn(string $state): string =>
                        Role::all()->firstWhere('label', $state)?->color_template ?? 'gray'
                    ),
                ToggleColumn::make('active')
                    ->onIcon(Heroicon::Check)
                    ->offIcon(Heroicon::XMark)
                    ->disabled(fn($record) => Auth::id() === $record->id),
            ])
            ->filters([
                SelectFilter::make('active')
                    ->options(Status::class)
                    ->default(1)
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
            ])
            ->paginated(true);
    }
}
