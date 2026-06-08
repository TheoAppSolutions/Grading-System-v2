<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Filament\App\Resources\Teacher\Grades\GradeResource;
use Filament\Facades\Filament;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages;
use Filament\Support\Icons\Heroicon;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Amber,
                'secondary' => Color::Slate,
                'success' => Color::Green,
                'danger' => Color::Red,
                'warning' => Color::Yellow,
                'info' => Color::Blue,
                'gray' => Color::Gray,
            ])
            ->breadcrumbs(false)
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([Pages\Dashboard::class,])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $autoItems = collect(Filament::getResources())
                    ->filter(fn($resource) => $resource::shouldRegisterNavigation())
                    ->flatMap(fn($resource) => $resource::getNavigationItems())
                    ->all();

                $autoGroups = collect($autoItems)
                    ->groupBy(fn($item) => $item->getGroup() ?? '')
                    ->map(fn($items, $groupLabel) => NavigationGroup::make($groupLabel)->items($items->all()))
                    ->values()
                    ->all();

                $groups = [...$autoGroups];

                $groups[] = NavigationGroup::make('Developer Tools')->items([
                    NavigationItem::make('Migrations')
                        ->icon(Heroicon::ArrowPathRoundedSquare)
                        ->url('/admin/migrations')
                        ->isActiveWhen(fn() => request()->is('admin/migrations')),
                    NavigationItem::make('Logs')
                        ->icon(Heroicon::ExclamationTriangle)
                        ->url('/telescope')
                        ->openUrlInNewTab(),
                ]);

                return $builder->groups($groups);
            });
    }
}
