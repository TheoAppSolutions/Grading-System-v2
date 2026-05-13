<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('attendances')]
class Attendance extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Helpers
    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('schoolyear', $schoolyear);
    }

    public function scopeByMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    public static function getTotalPresent(int $studentId, string $schoolyear): int
    {
        return static::where('student_id', $studentId)
            ->where('schoolyear', $schoolyear)
            ->sum('days_present');
    }

    public static function getTotalTardiness(int $studentId, string $schoolyear): float
    {
        return static::where('student_id', $studentId)
            ->where('schoolyear', $schoolyear)
            ->sum('days_tardiness');
    }

    public static function record(
        int $studentId,
        string $schoolyear,
        int $month,
        float $daysPresent,
        float $daysTardiness = 0
    ): void {
        static::updateOrCreate(
            [
                'student_id' => $studentId,
                'schoolyear' => $schoolyear,
                'month'      => $month,
            ],
            [
                'days_present'   => $daysPresent,
                'days_tardiness' => $daysTardiness,
            ]
        );
    }
}
