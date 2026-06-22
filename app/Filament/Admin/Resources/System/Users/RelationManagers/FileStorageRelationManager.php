<?php

namespace App\Filament\Admin\Resources\System\Users\RelationManagers;

use App\Filament\Admin\Resources\System\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class FileStorageRelationManager extends RelationManager
{
    protected static string $relationship = 'fileStorage';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('upload_file')
                    ->label('Upload File'),
            ])
            ->emptyStateHeading('No Files Found');
    }
}
