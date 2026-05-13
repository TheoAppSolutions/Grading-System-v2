<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('deportments')]
class Deportment extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public static function getScore(
        int $studentId,
        int $userId,
        int $quarter,
        string $schoolyear
    ): ?float {
        return static::where('student_id', $studentId)
            ->where('user_id', $userId)
            ->where('quarter', $quarter)
            ->where('school_year', $schoolyear)
            ->value('deportment');
    }
}
