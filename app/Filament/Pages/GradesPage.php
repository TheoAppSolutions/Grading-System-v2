<?php

namespace App\Filament\Pages;

use App\Filament\App\Resources\Office\Students\StudentResource;
use App\Helpers\MyTools;
use App\Models\Config\GradeLevel;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Override;
use UnitEnum;

class GradesPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Teacher Menu';

    protected static ?string $title = 'Grades';

    protected static ?string $slug = 'Grades';

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
        $subject = Subject::find(request('sid'));
        return $subject->subject_title . ' (S.Y. ' . SchoolYear::getActive()->label . ')';
    }

    public function table(Table $table): Table
    {
        $schoolYear = SchoolYear::getActive();
        $gradeLevelId = request('gid');
        $subjectId = request('sid');

        return $table
            ->query(
                Student::query()
                    ->where('active', 1)
                    ->whereHas('enrollments', fn($q) => $q
                        ->where('school_year_id', $schoolYear->id)
                        ->where('grade_level_id', $gradeLevelId))
                    ->leftJoin('grades', function ($join) use ($subjectId, $schoolYear) {
                        $join->on('grades.student_id', '=', 'students.id')
                            ->where('grades.subject_id', $subjectId)
                            ->where('grades.school_year_id', $schoolYear->id);
                    })
                    ->select('students.*', 'grades.*')
            )
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->label('Student ID'),
                TextColumn::make('name')
                    ->getStateUsing(fn($record) => $record['last_name'] . ', ' . $record['first_name'])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('midquarter')
                    ->label('MidaQuarter'),
                TextColumn::make('final')
                    ->label('Final'),
                TextColumn::make('effort')
                    ->label('Effort'),
                TextColumn::make('deportment')
                    ->label('Deportment'),
            ]);
    }
}
