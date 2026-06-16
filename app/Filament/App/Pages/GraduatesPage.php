<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\Office\Students\StudentResource;
use App\Helpers\MyTools;
use App\Models\Graduate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Override;
use UnitEnum;

class GraduatesPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Graduate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?string $title = 'Graduates';

    protected static ?string $slug = 'Graduates';

    protected string $view = 'filament.app.pages.custom-tables-page';

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        $schoolYear = MyTools::getSchoolYear();

        if ($schoolYear && Auth::user()->isOffice()) {
            return true;
        }

        return false;
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Graduates';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Graduate::query()
            )
            ->columns([
                TextColumn::make('student_id')
                    ->label('Student ID'),
                TextColumn::make('student')
                    ->getStateUsing(fn ($record) => $record->student->fullName)
                    ->searchable(),
                TextColumn::make('schoolYear.school_year')
                    ->label('Graduate School Year'),
            ])
            ->defaultSort('school_year', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->color('success')
                    ->url(fn ($record) => StudentResource::getUrl('view', [$record->student_id])),
                Action::make('viewTOR')
                    ->label('TOR')
                    ->icon(Heroicon::Document)
                    ->button()
                    ->tooltip('Transcript of Records')
                    ->color('warning'),
            ]);
    }
}
