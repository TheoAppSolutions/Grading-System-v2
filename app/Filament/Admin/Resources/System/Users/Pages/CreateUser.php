<?php

namespace App\Filament\Admin\Resources\System\Users\Pages;

use App\Filament\Admin\Resources\System\Users\UserResource;
use App\Models\Setting;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['active'] = 1;
        $data['password'] = bcrypt(Setting::get('default_password', '654321'));
        return parent::mutateFormDataBeforeCreate($data);
    }
}
