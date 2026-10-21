<?php

namespace App\Filament\Pages;

use App\Models\FileStorage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class FileStoragePage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = FileStorage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'System Settings';

    protected static ?string $title = 'File Storage';

    protected static ?string $slug = 'File Storage';

    protected string $view = 'filament.app.pages.custom-tables-page';

    protected static ?int $navigationSort = 3;

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'File Storage';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FileStorage::query()->with('uploadedBy')
            )
            ->columns([
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable(),
                TextColumn::make('uploadedBy.name')
                    ->label('Uploaded by')
                    ->searchable()
                    ->placeholder('Unknown'),
                TextColumn::make('file_extension')
                    ->label('Extension')
                    ->getStateUsing(fn (FileStorage $record): string => pathinfo(
                        $record->file_name ?: $record->hashed_filename ?: '',
                        PATHINFO_EXTENSION,
                    ) ?: '—'),
                TextColumn::make('uploaded_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->button()
                    ->icon(Heroicon::ArrowDownTray)
                    ->url(fn (FileStorage $record): string => route(
                        'admin.file-storage.download',
                        $record,
                    )),
                DeleteAction::make()
                    ->button()
                    ->before(function (FileStorage $record): void {
                        if (filled($record->hashed_filename)) {
                            Storage::disk('public')->delete($record->hashed_filename);
                        }
                    }),
            ])
            ->emptyStateHeading('No Files Found');
    }
}
