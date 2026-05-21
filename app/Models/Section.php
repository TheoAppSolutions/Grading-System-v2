<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Sushi;

#[Table('sections')]
#[Unguarded]
class Section extends Model
{
    // Relationships
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(Config\GradeLevel::class, 'grade_level_id', 'id');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'id');
    }

    public function homeroom(): HasMany
    {
        return $this->hasMany(Homeroom::class, 'section_id', 'id');
    }

    public function enrollment(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'section_id', 'id');
    }

    public function subjectAssignments(): HasMany
    {
        return $this->hasMany(SubjectUser::class, 'section_id', 'id');
    }
}
