<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('grades')]
#[Unguarded]
class Grade extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id', 'id');
    }

    // Helpers
    public function isPassed(): bool
    {
        return strtoupper($this->remarks) === 'PASSED';
    }

    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year_id', $schoolyear);
    }

    public function scopeByQuarter($query, int $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    public function scopeByGradeLevel($query, int $gradeLevel)
    {
        return $query->where('grade_level_id', $gradeLevel);
    }

    public function scopeBySubject($query, string $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopePassed($query)
    {
        return $query->where('remarks', 'PASSED');
    }

    public function scopeFailed($query)
    {
        return $query->where('remarks', 'FAILED');
    }
}
