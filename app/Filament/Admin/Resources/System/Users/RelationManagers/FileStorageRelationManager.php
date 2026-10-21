<?php

namespace App\Filament\Admin\Resources\System\Users\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\FileStorage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class FileStorageRelationManager extends RelationManager
{
    protected static string $relationship = 'fileStorage';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable(),
                TextColumn::make('file_type')
                    ->label('Type'),
                TextColumn::make('uploaded_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('upload_files')
                    ->label('Upload Files')
                    ->schema([
                        FileUpload::make('files')
                            ->label('User Files')
                            ->multiple()
                            ->disk('public')
                            ->directory(fn () => 'users/' . $this->getOwnerRecord()->getKey() . '/files')
                            ->visibility('public')
                            ->downloadable()
                            ->previewable()
                            ->reorderable()
                            ->storeFileNamesIn('file_names')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $owner = $this->getOwnerRecord();

                        foreach ($data['files'] ?? [] as $path) {
                            FileStorage::create([
                                'owner_id' => $owner->getKey(),
                                'owner_type' => $owner::class,
                                'uploaded_by' => auth()->id(),
                                'file_name' => $data['file_names'][$path] ?? basename($path),
                                'file_type' => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
                                'hashed_filename' => $path,
                                'uploaded_at' => Carbon::now(),
                            ]);
                        }
                    }),
            ])
            ->emptyStateHeading('No Files Found');
    }
}
