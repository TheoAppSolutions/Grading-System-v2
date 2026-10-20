<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\ReadAnnouncementsWidget;
use App\Filament\Widgets\ActiveAnnouncementsWidget;
use Override;

class DashboardPage extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    #[Override]
    public function getWidgets(): array
    {
        return [
            ActiveAnnouncementsWidget::class,
            ReadAnnouncementsWidget::class,
        ];
    }
}
