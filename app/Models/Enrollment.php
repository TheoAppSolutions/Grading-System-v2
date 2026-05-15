<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('enrollments')]
#[Unguarded]
class Enrollment extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'gradelevel', 'code');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(Schoolyear::class, 'school_year', 'school_year');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

    // Helpers
    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year', $schoolyear);
    }

    public function scopeByGradeLevel($query, string $gradelevel)
    {
        return $query->where('gradelevel', $gradelevel);
    }

    public static function isEnrolled(int $studentId, string $schoolyear): bool
    {
        return static::where('student_id', $studentId)
            ->where('school_year', $schoolyear)
            ->exists();
    }
}
