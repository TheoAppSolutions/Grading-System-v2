<?php

namespace App\Filament\Admin\Resources\Enrollments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EnrollmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.name')
                    ->label('Student'),
                TextEntry::make('school_year'),
                TextEntry::make('grade_level')
                    ->placeholder('-'),
                TextEntry::make('section_id')
                    ->placeholder('-'),
            ]);
    }
}
