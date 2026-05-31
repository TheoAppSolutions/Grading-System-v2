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
use App\Models\SubjectUser;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use App\Helpers\MyTools;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
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
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {

                $schoolYear = MyTools::getSchoolYear(); // move this up before it's used

                $autoItems = collect(Filament::getResources())
                    ->reject(fn($resource) => $resource === GradeResource::class)
                    ->flatMap(fn($resource) => $resource::getNavigationItems())
                    ->all();

                $autoGroups = collect($autoItems)
                    ->groupBy(fn($item) => $item->getGroup() ?? '')
                    ->map(fn($items, $groupLabel) => NavigationGroup::make($groupLabel)->items($items->all()))
                    ->values()
                    ->all();

                $groups = [...$autoGroups];

                if ($schoolYear) {
                    $subjectGroups = $this->buildSubjectNavItems($schoolYear);

                    if (!empty($subjectGroups)) {
                        $groups = [...$subjectGroups, ...$groups]; // spread since it's now multiple groups
                    }
                }

                return $builder->groups($groups);
            });
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
            ->groupBy(fn(SubjectUser $subjectUser) => $subjectUser->subject->subjectCategory->label)
            ->map(function ($groupedSubjects, string $subjectTitle) {
                $items = $groupedSubjects
                    ->values()
                    ->map(function (SubjectUser $subjectUser, int $index) {
                        $subject = $subjectUser->subject;

                        return NavigationItem::make(MyTools::ordinal($subject->grade_level_id) . ' Grade')
                            ->url(GradeResource::getUrl('index') . '?sid=' . $subjectUser->subject_id . '&gid=' . $subjectUser->subject->grade_level_id)
                            ->isActiveWhen(fn() => request()->is('app/teacher/grades*') && request('sid') == $subjectUser->subject_id)
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
