<?php

namespace App\Filament\Admin\Resources\System\Users\RelationManagers;

use App\Models\Config\Role;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UserRolesRelationManager extends RelationManager
{
    protected static string $relationship = 'userRoles'; // or 'userRoles' — must match User model

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->heading('Access Rights')
            ->columns([
                TextColumn::make('role.label')
                    ->label('Role'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Assign Role')
                    ->modalSubmitActionLabel('Assign')
                    ->createAnother(false)
                    ->label('Assign Role')
                    ->schema([
                        Select::make('role_id')
                            ->label('Role')
                            ->options(Role::all()->pluck('label', 'id'))
                            ->searchable()
                            ->required(),
                    ]),
            ])
            ->recordActions([
                DeleteAction::make()->label('Remove'),
            ])
            ->paginated(false);
    }
}
