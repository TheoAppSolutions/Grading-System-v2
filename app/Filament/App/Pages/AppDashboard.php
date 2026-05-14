<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class AppDashboard extends Page
{
    protected string $view = 'filament.app.pages.app-dashboard';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Home;
}
