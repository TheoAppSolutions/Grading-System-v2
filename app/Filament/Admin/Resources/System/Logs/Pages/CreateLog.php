<?php

namespace App\Filament\Admin\Resources\System\Logs\Pages;

use App\Filament\Admin\Resources\System\Logs\LogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLog extends CreateRecord
{
    protected static string $resource = LogResource::class;
}
