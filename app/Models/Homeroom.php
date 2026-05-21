<?php

namespace App\Models;

use App\Models\Config\GradeLevel;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('homerooms')]
#[Unguarded]
class Homeroom extends Model
{
    // Relationships
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id');
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coAdviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id', 'id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(Schoolyear::class, 'school_year_id', 'id');
    }

    // Helpers
    public function hasCoAdviser(): bool
    {
        return !is_null($this->user2_id);
    }
}
