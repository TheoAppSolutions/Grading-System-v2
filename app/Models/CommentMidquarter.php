<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('comments_midquarter')]
#[Unguarded]
class CommentMidquarter extends Model
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

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(GradeCriteria::class, 'criteria_id');
    }

    // Helpers
    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year', $schoolyear);
    }

    public function scopeByQuarter($query, int $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    public function scopeBySubject($query, string $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public static function getScore(
        int $studentId,
        string $subjectId,
        int $criteriaId,
        int $quarter,
        string $schoolyear
    ): ?string {
        return static::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('criteria_id', $criteriaId)
            ->where('quarter', $quarter)
            ->where('school_year', $schoolyear)
            ->value('score');
    }
}
