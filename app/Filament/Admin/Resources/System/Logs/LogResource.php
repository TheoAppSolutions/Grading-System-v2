<?php

namespace App\Filament\Admin\Resources\System\Logs;

use App\Filament\Admin\Resources\System\Logs\Pages\CreateLog;
use App\Filament\Admin\Resources\System\Logs\Pages\EditLog;
use App\Filament\Admin\Resources\System\Logs\Pages\ListLogs;
use App\Filament\Admin\Resources\System\Logs\Pages\ViewLog;
use App\Filament\Admin\Resources\System\Logs\Schemas\LogForm;
use App\Filament\Admin\Resources\System\Logs\Schemas\LogInfolist;
use App\Filament\Admin\Resources\System\Logs\Tables\LogsTable;
use App\Models\Log;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogResource extends Resource
{
    protected static ?string $model = Log::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'System Settings';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogs::route('/'),
            'create' => CreateLog::route('/create'),
            'view' => ViewLog::route('/{record}'),
            'edit' => EditLog::route('/{record}/edit'),
        ];
    }
}
