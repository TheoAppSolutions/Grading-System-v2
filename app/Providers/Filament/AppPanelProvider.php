<?php

namespace App\Providers\Filament;

use App\Filament\Pages;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Helpers\MyTools;
use App\Models\SubjectUser;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\Facehash\Enums\Variant;
use Saade\FilamentFacehash\FacehashPlugin;
use Saade\FilamentFacehash\FacehashProvider;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
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
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Pages\DashboardPage::class,
                Pages\HomeroomsPage::class,
                Pages\EnrollmentsPage::class,
                Pages\GraduatesPage::class,
                Pages\GradesPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
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
            ->defaultAvatarProvider(FacehashProvider::class)
            ->plugins([
                FacehashPlugin::make()
                    ->size(40)                       // Avatar size in pixels (default: 40)
                    ->variant(Variant::Gradient)     // Variant::Gradient or Variant::Solid (default: Gradient)
                    ->initial(true)                  // Show first letter of name (default: true)
                    ->colors([                       // Custom color palette
                        '#ec4899',
                        '#f59e0b',
                        '#3b82f6',
                        '#f97316',
                        '#10b981',
                    ]),
            ])
            ->userMenuItems([
                'Admin Page' => fn (Action $action) => $action->make('app')
                    ->label('Admin Page')
                    ->visible(Auth::user()->isAdmin())
                    ->icon(Heroicon::Bolt)
                    ->action(fn () => redirect(url('admin'))),
                'logout' => fn (Action $action) => $action->label('Log out'),
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $schoolYear = MyTools::getSchoolYear();
                $autoItems = collect(Filament::getResources())
                    ->filter(fn ($resource) => $resource::shouldRegisterNavigation())
                    ->flatMap(fn ($resource) => $resource::getNavigationItems());

                $pageItems = collect([
                    ...Pages\DashboardPage::getNavigationItems(),
                    ...Pages\HomeroomsPage::getNavigationItems(),
                    ...Pages\EnrollmentsPage::getNavigationItems(),
                    ...Pages\GraduatesPage::getNavigationItems(),
                ]);

                // Pages that declare a navigationGroup join the grouped pool;
                // pages without one stay as flat top-level items.
                $groupedPageItems = $pageItems->filter(fn ($item) => $item->getGroup() !== null);
                $ungroupedPageItems = $pageItems->reject(fn ($item) => $item->getGroup() !== null);

                $groups = $autoItems->merge($groupedPageItems)
                    ->sortBy(fn ($item) => $item->getSort())
                    ->groupBy(fn ($item) => $item->getGroup())
                    ->map(fn ($items, $groupLabel) => NavigationGroup::make($groupLabel)->items($items->values()->all()))
                    ->values()
                    ->all();

                if ($schoolYear) {
                    $subjectGroups = $this->buildSubjectNavItems($schoolYear);

                    if (! empty($subjectGroups)) {
                        $groups = [...$subjectGroups, ...$groups];
                    }
                }

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
            });
        // ->renderHook(PanelsRenderHook::BODY_END, function () {
        //     return view('filament.footer');
        // });
    }

    private function buildSubjectNavItems($schoolYear): array
    {
        $subjects = SubjectUser::query()
            ->where('user_id', Auth::id())
            ->where('school_year_id', $schoolYear->id)
            ->with('subject')
            ->get();

        if ($subjects->isEmpty()) {
            return [];
        }

        return $subjects
            ->groupBy(fn (SubjectUser $subjectUser) => $subjectUser->subject->subjectCategory->label)
            ->map(function ($groupedSubjects, string $subjectTitle) {
                $items = $groupedSubjects
                    ->values()
                    ->map(function (SubjectUser $subjectUser, int $index) {
                        $subject = $subjectUser->subject;

                        return NavigationItem::make(MyTools::ordinal($subject->grade_level_id).' Grade')
                            ->url(Pages\GradesPage::getUrl(['sid' => $subjectUser->subject_id, 'gid' => $subjectUser->subject->grade_level_id]))
                            ->isActiveWhen(fn () => request()->is('app/Grades*') && request('sid') == $subjectUser->subject_id)
                            ->sort($index);
                    })
                    ->all();

                return NavigationGroup::make($subjectTitle)
                    ->items($items);
            })
            ->values()
            ->all();
    }
}
