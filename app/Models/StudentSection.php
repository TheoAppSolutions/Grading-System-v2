<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('student_sections')]
class StudentSection extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Helpers
    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year', $schoolyear);
    }

    public static function getCurrentSection(int $studentId, string $schoolyear): ?string
    {
        return static::where('student_id', $studentId)
            ->where('school_year', $schoolyear)
            ->value('section');
    }
}
