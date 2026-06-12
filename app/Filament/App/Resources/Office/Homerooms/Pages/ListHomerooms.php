<?php

namespace App\Filament\App\Resources\Office\Homerooms\Pages;

use App\Filament\App\Resources\Office\Homerooms\HomeroomResource;
use App\Models\SchoolYear;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListHomerooms extends ListRecords
{
    protected static string $resource = HomeroomResource::class;

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return "Homeroom (S.Y. " . SchoolYear::getActive()->school_year .")";
    }
}
