<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Marcelodelgado\Announcements\Filament\Widgets\AnnouncementsWidget;
use Override;

class DashboardPage extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    #[Override]
    public function getWidgets(): array
    {
        return [
            AnnouncementsWidget::class
        ];
    }
}
