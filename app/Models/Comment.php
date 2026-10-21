<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('comments')]
#[Unguarded]
class Comment extends Model
{
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    // Helpers
    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year_id', $schoolyear);
    }

}
