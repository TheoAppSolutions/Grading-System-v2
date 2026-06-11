<?php

namespace App\Filament\App\Resources\Office\CommentCriterias\Pages;

use App\Filament\App\Resources\Office\CommentCriterias\CommentCriteriaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCommentCriteria extends EditRecord
{
    protected static string $resource = CommentCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
