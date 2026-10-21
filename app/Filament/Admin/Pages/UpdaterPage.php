<?php

namespace App\Filament\Admin\Pages;

use App\Services\GitUpdater;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Override;
use Throwable;

class UpdaterPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ArrowPath;

    protected static ?string $navigationLabel = 'Updater';

    protected static ?string $title = 'Application updater';

    protected static ?string $slug = 'updater';

    protected static ?int $navigationSort = 4;

    protected static string|\UnitEnum|null $navigationGroup = 'Developer Tools';

    protected string $view = 'filament.admin.pages.updater-page';

    /** @var array{available?: bool, local?: string, local_short?: string, local_date?: string, error?: string} */
    public array $status = [];

    public bool $hasCheckedForUpdates = false;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()?->isAdmin() === true;
    }

    public function mount(): void
    {
        $this->refreshInstalledVersion();
    }

    #[Override]
    public function getHeading(): string|Htmlable
    {
        return 'Application updater';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkForUpdates')
                ->label('Check for updates')
                ->icon(Heroicon::ArrowPath)
                ->action('checkForUpdates'),
            Action::make('update')
                ->label('Update application')
                ->icon(Heroicon::ArrowUpCircle)
                ->color('primary')
                ->visible(fn (): bool => $this->hasCheckedForUpdates && ($this->status['available'] ?? false) === true)
                ->requiresConfirmation()
                ->action('updateApplication'),
        ];
    }

    public function checkForUpdates(): void
    {
        $this->refreshStatus();

        Notification::make()
            ->title(($this->status['available'] ?? false) ? 'Update available' : 'Already up to date')
            ->body(($this->status['available'] ?? false)
                ? 'A newer commit is available.'
                : 'No new update was found.')
            ->color(($this->status['available'] ?? false) ? 'warning' : 'success')
            ->send();
    }

    public function updateApplication(): void
    {
        try {
            $result = app(GitUpdater::class)->update();

            Notification::make()
                ->title($result['updated'] ? 'Application updated' : 'Already up to date')
                ->body($result['message'])
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Update failed')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }

        $this->refreshInstalledVersion();
        $this->hasCheckedForUpdates = true;
    }

    private function refreshStatus(): void
    {
        try {
            $this->status = app(GitUpdater::class)->status();
        } catch (Throwable $exception) {
            report($exception);
            $this->status = ['error' => 'Unable to check the remote repository.'];
        }
    }

    private function refreshInstalledVersion(): void
    {
        try {
            $this->status = app(GitUpdater::class)->current();
        } catch (Throwable $exception) {
            report($exception);
            $this->status = ['error' => 'Unable to determine the installed version.'];
        }
    }
}
