<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Honorific: string implements HasLabel
{
    case Mr  = 'Mr.';
    case Ms  = 'Ms.';
    case Mrs = 'Mrs.';

    public function getLabel(): string
    {
        return $this->value;
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
