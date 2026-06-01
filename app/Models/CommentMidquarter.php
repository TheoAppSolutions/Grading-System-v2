<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sushi\Sushi;

// #[Table('comments_midquarter')]
// #[Unguarded]
class CommentMidquarter extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'label' => 'Custom Field 1',
        ],
        [
            'id' => 2,
            'label' => 'Custom Field 2',
        ],
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
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
        return $query->where('school_year_id', $schoolyear);
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
            ->where('school_year_id', $schoolyear)
            ->value('score');
    }
}
