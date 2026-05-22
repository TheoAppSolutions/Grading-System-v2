<?php

namespace App\Filament\Admin\Resources\System\Users\RelationManagers;

use App\Models\Config\Role;
use Filament\Actions\AssociateAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;

class UserRolesRelationManager extends RelationManager
{
    protected static string $relationship = 'userRoles';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->label('Add Role')
                    ->modalHeading('Add Role')
                    ->schema([
                        Select::make('role_id')
                            ->label('Role')
                            ->options(Role::all()->pluck('label', 'id'))
                    ])
            ]);
    }
}
