<?php

namespace App\Providers\Filament;

use App\Filament\App\Resources\Office\Students\StudentResource;
use App\Filament\Pages;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\BackupsPage;
use App\Filament\Pages\HealthCheckResultsPage;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->profile(EditProfile::class, isSimple: false)
            ->globalSearch(false)
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
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->resources([
                StudentResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Pages\DashboardPage::class,
                Pages\HomeroomsPage::class,
                Pages\EnrollmentsPage::class,
                Pages\GraduatesPage::class,
                Pages\GradesPage::class,
                BackupsPage::class,
                HealthCheckResultsPage::class,
            ])
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
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(HealthCheckResultsPage::class),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->navigationIcon('heroicon-o-cpu-chip')
                    ->navigationLabel('Backups')
                    ->navigationGroup('Settings')
                    ->navigationSort(3)
                    ->usingPage(BackupsPage::class)
            ])
            ->userMenuItems([
                'Office' => fn (Action $action) => $action->make('app')
                    ->label('Office Page')
                    ->icon(Heroicon::BuildingOffice2)
                    ->action(fn () => redirect(url('app'))),
                'logout' => fn (Action $action) => $action->label('Log out'),
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $autoItems = collect(Filament::getResources())
                    ->filter(fn ($resource) => $resource::shouldRegisterNavigation())
                    ->flatMap(fn ($resource) => $resource::getNavigationItems());

                $pageItems = collect([
                    ...Pages\DashboardPage::getNavigationItems(),
                    ...Pages\HomeroomsPage::getNavigationItems(),
                    ...Pages\EnrollmentsPage::getNavigationItems(),
                    ...Pages\GraduatesPage::getNavigationItems(),
                    ...BackupsPage::getNavigationItems(),
                    ...HealthCheckResultsPage::getNavigationItems(),
                ]);

                $groupedPageItems = $pageItems->filter(fn ($item) => $item->getGroup() !== null);
                $ungroupedPageItems = $pageItems->reject(fn ($item) => $item->getGroup() !== null);

                $groups = $autoItems->merge($groupedPageItems)
                    ->sortBy(fn ($item) => $item->getSort())
                    ->groupBy(fn ($item) => $item->getGroup())
                    ->map(fn ($items, $groupLabel) => NavigationGroup::make($groupLabel)->items($items->values()->all()))
                    ->values()
                    ->all();

                $groups[] = NavigationGroup::make('Developer Tools')->items([
                    NavigationItem::make('Logs')
                        ->icon(Heroicon::ExclamationTriangle)
                        ->url('/telescope')
                        ->openUrlInNewTab(),
                ]);

                return $builder
                    ->items([
                        ...$ungroupedPageItems->sortBy(fn ($item) => $item->getSort())->all(),
                        NavigationItem::make('Logout')
                            ->url("javascript:document.querySelector('#logout').submit();")
                            ->icon('heroicon-m-arrow-left-on-rectangle')
                            ->sort(100),
                    ])
                    ->groups($groups);
            })
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_END, function () {
                return Blade::render('<form action="{{ $logoutLink }}" method="post" id="logout">@csrf</form>', [
                    'logoutLink' => filament()->getLogoutUrl(),
                ]);
            })
            ->renderHook(PanelsRenderHook::BODY_END, function () {
                return view('filament.footer');
            });
    }
}
