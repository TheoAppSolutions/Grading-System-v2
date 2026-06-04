<?php

namespace App\Filament\Admin\Pages;

use App\Http\Controllers\Migration\EnrollmentMigrationController;
use App\Http\Controllers\Migration\SchoolYearMigrationController;
use App\Http\Controllers\Migration\SectionMigrationController;
use App\Http\Controllers\Migration\StudentMigrationController;
use App\Http\Controllers\Migration\SubjectMigrationController;
use App\Http\Controllers\Migration\SubjectUnitMigrationController;
use App\Http\Controllers\Migration\SubjectUserMigrationController;
use App\Http\Controllers\Migration\UserMigrationController;
use App\Http\Controllers\Migration\GradeMigrationController;
use App\Models\Config\Migration;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class MigrationPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowPathRoundedSquare;

    protected static string|UnitEnum|null $navigationGroup = 'System Settings';

    protected static ?string $navigationLabel = 'Migrations';

    protected static ?string $title = 'Migrations';

    protected string $view = 'filament.admin.pages.migration-page';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'migrations';

    public string $output = '';

    public function table(Table $table): Table
    {
        $oldDbAvailable = self::isOldDbAvailable();

        return $table
            ->query(Migration::query())
            ->defaultGroup('step')
            ->columns([
                TextColumn::make('description'),
                TextColumn::make('old_database')
                    ->label('Old Database')
                    ->getStateUsing(fn(Migration $record) => DB::connection('old_db')->table($record->old_table)->count()),
                TextColumn::make('new_database')
                    ->label('New Database')
                    ->getStateUsing(fn(Migration $record) => DB::table($record->new_table)->count()),
            ])
            ->recordActions([
                Action::make('migrate')
                    ->label('Migrate')
                    ->action(function (Migration $record) {
                        $controller = $this->resolveController($record->new_table);
                        if (! $controller) {
                            return;
                        }

                        if($record->new_table === 'grades') {
                            try {
                                $result = $controller->migrateTable(str_replace('grades_', '', $record->old_table));
                            } catch (\Exception $e) {
                                $this->output = "Error migrating {$record->old_table}: {$e->getMessage()}";
                                Notification::make()->title('Migration failed')->danger()->send();
                                return;
                            }
                        } else {
                            try {
                                $result = $controller->migrate();
                            } catch (\Exception $e) {
                                $this->output = "Error migrating {$record->old_table}: {$e->getMessage()}";
                                Notification::make()->title('Migration failed')->danger()->send();
                                return;
                            }
                        }
                        $this->output = "{$result}";

                        Notification::make()->title('Migration complete')->success()->send();
                    })
                    ->button(),

                ActionGroup::make([
                    Action::make('reset')
                        ->label('Reset')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('This will delete all migrated data. Are you sure?')
                        ->action(function (Migration $record) {
                            $controller = $this->resolveController($record->new_table);
                            if (! $controller) {
                                return;
                            }

                            $result = $controller->reset();
                            $this->output = "{$result}";

                            Notification::make()->title('Reset complete')->success()->send();
                        }),

                    Action::make('additional')
                        ->label(fn(Migration $record) => str($record->additional)->headline()->toString())
                        ->visible(fn(Migration $record) => ! empty($record->additional))
                        ->color('info')
                        ->action(function (Migration $record) {
                            $controller = $this->resolveController($record->new_table);
                            if (! $controller || ! method_exists($controller, $record->additional)) {
                                return;
                            }

                            $result = $controller->{$record->additional}();
                            $this->output = "{$result}";

                            Notification::make()->title('Complete')->success()->send();
                        }),
                ])
                    ->label('Actions')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->color('')
                    ->button()

            ])
            ->emptyStateIcon('heroicon-o-server')
            ->when(
                ! $oldDbAvailable,
                fn(Table $table) => $table
                    ->emptyStateHeading('Old database unavailable')
                    ->emptyStateDescription('Could not connect to the old database. Check your DB_OLD_* credentials in .env.')
                    ->query(Migration::query()->whereRaw('1 = 0')) // force empty
            )
            ->paginated(false);
    }

    public function form(Schema $schema): Schema
    {
        $oldDbAvailable = self::isOldDbAvailable();

        return $oldDbAvailable ? $schema
            ->schema([
                Textarea::make('output')
                    ->label('Migration Output')
                    ->readonly()
                    ->autosize()
                    ->columnSpanFull(),
            ]) : $schema->schema([]);
    }

    private function resolveController(string $table): ?object
    {
        return match ($table) {
            'users' => new UserMigrationController,
            'students' => new StudentMigrationController,
            'subjects' => new SubjectMigrationController,
            'school_years' => new SchoolYearMigrationController,
            'sections' => new SectionMigrationController,
            'subject_users' => new SubjectUserMigrationController,
            'subject_units' => new SubjectUnitMigrationController,
            'enrollments' => new EnrollmentMigrationController,
            'grades' => new GradeMigrationController,
            default => null,
        };
    }

    private static function isOldDbAvailable(): bool
    {
        try {
            DB::connection('old_db')->getPdo();

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::isOldDbAvailable();
    }
}
