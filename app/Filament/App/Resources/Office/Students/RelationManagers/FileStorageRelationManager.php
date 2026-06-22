<?php

namespace App\Filament\App\Resources\Office\Students\RelationManagers;

use App\Filament\App\Resources\Office\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class FileStorageRelationManager extends RelationManager
{
    protected static string $relationship = 'fileStorage';

    protected static ?string $title = 'File Storage';

    public function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->headerActions([
                Action::make('Upload Files')
                    ->button()
                    ->modal()
                    ->schema([
                        FileUpload::make('files')
                            ->label('Student Files')
                            ->multiple()
                            ->disk('public')
                            ->directory(fn($record) => 'students/' . $record?->id . '/files')
                            ->visibility('public')
                            ->downloadable()
                            ->previewable()
                            ->appendFiles()
                            ->reorderable(),
                    ])
            ])
            ->emptyStateHeading('No Files Found.');
    }
}
