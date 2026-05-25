<?php

namespace App\Filament\App\Resources\Office\Enrollments\Pages;

use App\Filament\App\Resources\Office\Enrollments\EnrollmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEnrollments extends ListRecords
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
