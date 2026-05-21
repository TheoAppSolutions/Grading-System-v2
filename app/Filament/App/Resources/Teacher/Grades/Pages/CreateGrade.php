<?php

namespace App\Filament\App\Resources\Teacher\Grades\Pages;

use App\Filament\App\Resources\Teacher\Grades\GradeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;
}
