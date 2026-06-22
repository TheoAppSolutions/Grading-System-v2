<?php

namespace App\Filament\Pages;

use App\Models\FileStorage;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
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
                FileStorage::query()
            );
    }
}
