<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('deportments')]
#[Unguarded]
class Deportment extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public static function getScore(
        int $studentId,
        int $userId,
        int $quarter,
        string $schoolyear
    ): ?float {
        return static::where('student_id', $studentId)
            ->where('user_id', $userId)
            ->where('quarter', $quarter)
            ->where('school_year_id', $schoolyear)
            ->value('deportment');
    }
}
