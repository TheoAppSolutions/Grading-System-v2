<?php

namespace App\Filament\App\Resources\Office\CommentCriterias\Pages;

use App\Filament\App\Resources\Office\CommentCriterias\CommentCriteriaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommentCriterias extends ListRecords
{
    protected static string $resource = CommentCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
