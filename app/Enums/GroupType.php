<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum GroupType: string implements HasLabel
{
    case Section = 1;
    case Strand  = 2;

    public function getLabel(): string
    {
        return match($this) {
            GroupType::Section => 'Section',
            GroupType::Strand  => 'Strand',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}