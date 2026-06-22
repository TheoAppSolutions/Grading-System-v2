<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: int implements HasLabel, HasColor
{
    case Active   = 1;
    case Inactive = 0;

    public function getLabel(): string
    {
        return match ($this) {
            Status::Active   => 'Active',
            Status::Inactive => 'Inactive',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            Status::Active   => 'success',
            Status::Inactive => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
