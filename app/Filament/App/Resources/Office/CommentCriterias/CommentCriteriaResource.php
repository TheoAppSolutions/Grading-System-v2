<?php

namespace App\Filament\App\Resources\Office\CommentCriterias;

use App\Filament\App\Resources\Office\CommentCriterias\Pages\CreateCommentCriteria;
use App\Filament\App\Resources\Office\CommentCriterias\Pages\EditCommentCriteria;
use App\Filament\App\Resources\Office\CommentCriterias\Pages\ListCommentCriterias;
use App\Filament\App\Resources\Office\CommentCriterias\Pages\ViewCommentCriteria;
use App\Filament\App\Resources\Office\CommentCriterias\Schemas\CommentCriteriaForm;
use App\Filament\App\Resources\Office\CommentCriterias\Schemas\CommentCriteriaInfolist;
use App\Filament\App\Resources\Office\CommentCriterias\Tables\CommentCriteriasTable;
use App\Models\CommentCriteria;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommentCriteriaResource extends Resource
{
    protected static ?string $model = CommentCriteria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenter;

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CommentCriteriaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommentCriteriaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommentCriteriasTable::configure($table);
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
            'index' => ListCommentCriterias::route('/'),
            'create' => CreateCommentCriteria::route('/create'),
            'view' => ViewCommentCriteria::route('/{record}'),
            'edit' => EditCommentCriteria::route('/{record}/edit'),
        ];
    }
}
