<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class AdminDashboard extends Page
{
    protected string $view = 'filament.admin.pages.admin-dashboard';
    
    protected static string | BackedEnum | null $navigationIcon = Heroicon::Home;
}
