<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Config\GradeLevel;

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
        return $this->belongsTo(GradeLevel::class, 'grade_level_id', 'label');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(Schoolyear::class, 'id', 'school_year_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

    public static function isEnrolled(int $studentId, string $schoolyear): bool
    {
        return static::where('student_id', $studentId)
            ->where('school_year_id', $schoolyear)
            ->exists();
    }
}
