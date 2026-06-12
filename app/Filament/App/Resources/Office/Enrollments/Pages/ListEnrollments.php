<?php

namespace App\Filament\App\Resources\Office\Enrollments\Pages;

use App\Filament\App\Resources\Office\Enrollments\EnrollmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\SchoolYear;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListEnrollments extends ListRecords
{
    protected static string $resource = EnrollmentResource::class;

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return "Enrollment (S.Y. " . SchoolYear::getActive()->school_year . ")";
    }
}
