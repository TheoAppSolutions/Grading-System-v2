<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Suffix: string implements HasLabel
{
    // Generational
    case Jr   = 'Jr.';
    case Sr   = 'Sr.';
    case II   = 'II';
    case III  = 'III';
    case IV   = 'IV';
    case V    = 'V';

    // Academic / Professional
    case PhD  = 'Ph.D.';
    case MD   = 'M.D.';
    case DDS  = 'D.D.S.';
    case DVM  = 'D.V.M.';
    case JD   = 'J.D.';
    case LLB  = 'LL.B.';
    case LLM  = 'LL.M.';
    case MBA  = 'M.B.A.';
    case MS   = 'M.S.';
    case MA   = 'M.A.';
    case BS   = 'B.S.';
    case BA   = 'B.A.';

    public function getLabel(): string
    {
        return $this->value;
    }

    public function group(): string
    {
        return match ($this) {
            self::Jr, self::Sr,
            self::II, self::III,
            self::IV, self::V    => 'Generational',
            default              => 'Academic / Professional',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }

    // For Filament grouped selects
    public static function groupedOptions(): array
    {
        return collect(self::cases())
            ->groupBy(fn($case) => $case->group())
            ->map(fn($cases) => $cases->mapWithKeys(fn($case) => [$case->value => $case->getLabel()]))
            ->toArray();
    }
}
