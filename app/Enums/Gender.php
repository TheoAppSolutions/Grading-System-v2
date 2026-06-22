<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel
{
    case Male   = 'M';
    case Female = 'F';

    public function getLabel(): string
    {
        return match($this) {
            Gender::Male   => 'Male',
            Gender::Female => 'Female',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}