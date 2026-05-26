<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('subject_excluded')]
#[Unguarded]
class SubjectExcluded extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    // Helper
    public static function isExcluded(int $studentId, string $subjectId, string $schoolyear): bool
    {
        return static::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('school_year', $schoolyear)
            ->exists();
    }
}
