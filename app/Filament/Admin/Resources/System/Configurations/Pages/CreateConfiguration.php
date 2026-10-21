<?php

namespace App\Filament\Admin\Resources\System\Configurations\Pages;

use App\Filament\Admin\Resources\System\Configurations\ConfigurationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConfiguration extends CreateRecord
{
    protected static string $resource = ConfigurationResource::class;
}
