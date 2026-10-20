<?php

namespace App\Filament\App\Resources\Office\CommentCriterias\Schemas;

use App\Models\Config\CommentCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentCriteriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('comment_category_id')
                    ->label('Category')
                    ->options(CommentCategory::query()->pluck('label', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('code')
                    ->required()
                    ->maxLength(10)
                    ->unique(modifyRuleUsing: fn ($rule, $get) => $rule->where('type', $get('type')), ignoreRecord: true),
                Select::make('type')
                    ->options([
                        1 => 'Midquarter',
                        2 => 'Quarter',
                    ])
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->default(true),
            ]);
    }
}
