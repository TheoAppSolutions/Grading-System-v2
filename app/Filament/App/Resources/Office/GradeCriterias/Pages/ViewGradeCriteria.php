<?php

namespace App\Filament\App\Resources\Office\CommentCriterias\Pages;

use App\Filament\App\Resources\Office\CommentCriterias\CommentCriteriaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCommentCriteria extends ViewRecord
{
    protected static string $resource = CommentCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
