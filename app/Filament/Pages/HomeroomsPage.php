<?php

namespace App\Filament\Pages;

use App\Helpers\MyTools;
use App\Models\Homeroom;
use App\Models\SchoolYear;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Override;
use UnitEnum;

class HomeroomsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Homeroom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?string $title = 'Homerooms';

    protected static ?string $slug = 'Homerooms';

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
        return 'Homerooms (S.Y. '.SchoolYear::getActive()->label.')';
    }

    public function table(Table $table): Table
    {
        $userList = User::pluck('name', 'id');
        $schoolYearActive = SchoolYear::getActive();

        return $table
            ->query(function () use ($schoolYearActive) {
                return Homeroom::where('school_year_id', $schoolYearActive->id);
            })
            ->columns([
                TextColumn::make('schoolYear.label'),
                TextColumn::make('gradeLevel.label'),
                TextColumn::make('section.section')
                    ->visible(config('app.system_features.sections')),
                SelectColumn::make('user_id')
                    ->label('Adviser')
                    ->searchableOptions()
                    ->options(function () use ($userList) {
                        return $userList;
                    }),
                SelectColumn::make('user2_id')
                    ->label('Co-Adviser')
                    ->searchableOptions()
                    ->options(function () use ($userList) {
                        return $userList;
                    }),
            ]);
    }
}
