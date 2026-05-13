<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('school_year_infos', timestamps: false)]
#[Unguarded]
class SchoolYearInfo extends Model
{
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    // Helpers
    public static function updateSchoolYearInfo(array $data): void
    {
        $school_year_id = SchoolYear::where('school_year', $data['school_year'])->first()->id;
        $school_year_id = $school_year_id ?? SchoolYear::latest()->first()->id + 1;

        $attendance_entry = array_map(function ($entry) {
            return (int) $entry ?? 0;
        }, $data['attendance_entry']);

        foreach ($attendance_entry as $index => $value) {
            SchoolYearInfo::updateOrCreate(
                ['school_year_id' => $school_year_id],
                [
                    'month'  => $index + 1,
                    'days'    => $value,
                ]
            );
        }
    }
}
