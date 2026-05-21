<?php

namespace App\Filament\Admin\Resources\System\Settings\Pages;

use App\Filament\Admin\Resources\System\Settings\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
}
