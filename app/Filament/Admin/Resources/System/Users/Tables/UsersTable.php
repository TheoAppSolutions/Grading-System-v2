<?php

namespace App\Filament\Admin\Resources\System\Users\Tables;

use App\Enums\Status;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Config\Role;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\HtmlString;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Image')
                    ->disk('public')
                    ->visibility('public')
                    ->circular()
                    ->imageHeight('3rem')
                    ->imageWidth('3rem')
                    ->defaultImageUrl(fn (User $record): string => $record->facehash_avatar_url)
                    ->action(
                        Action::make('view_avatar')
                            ->label('View image')
                            ->visible(fn (User $record): bool => filled($record->photo_path))
                            ->modalHeading(fn (User $record): string => $record->full_name . ' image')
                            ->modalWidth(Width::FitContent)
                            ->modalContent(function (User $record): HtmlString {
                                $imageUrl = $record->photo_url ?? $record->facehash_avatar_url;

                                return new HtmlString(
                                    '<div style="display: flex; justify-content: center; align-items: center;">'
                                    . '<img src="' . e($imageUrl) . '" alt="' . e($record->full_name) . '" '
                                    . 'style="display: block; width: auto; height: auto; max-width: 90vw; max-height: 70vh; object-fit: contain;" />'
                                    . '</div>'
                                );
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                    ),
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
