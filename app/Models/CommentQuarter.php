<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('comments_quarter')]
#[Unguarded]
class CommentQuarter extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(GradeCriteria::class, 'criteria_id');
    }

    // Helpers
    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year_id', $schoolyear);
    }

    public static function getScore(
        int $studentId,
        int $criteriaId,
        string $schoolyear
    ): ?string {
        return static::where('student_id', $studentId)
            ->where('criteria_id', $criteriaId)
            ->where('school_year_id', $schoolyear)
            ->value('score');
    }
}
