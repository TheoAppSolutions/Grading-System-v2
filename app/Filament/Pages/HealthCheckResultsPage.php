<?php

namespace App\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults as BaseHealthCheckResults;

class HealthCheckResultsPage extends BaseHealthCheckResults
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';

    public function getHeading(): string | Htmlable
    {
        return 'Health Check Results';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Developer Tools';
    }
}
