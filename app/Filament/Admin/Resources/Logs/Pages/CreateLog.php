<?php

namespace App\Filament\Admin\Resources\Logs\Pages;

use App\Filament\Admin\Resources\Logs\LogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLog extends CreateRecord
{
    protected static string $resource = LogResource::class;
}
