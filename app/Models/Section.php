<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('sections')]
#[Unguarded]
class Section extends Model
{
    // Relationships
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'gradelevel', 'code');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(Schoolyear::class, 'school_year', 'school_year');
    }

    public function homeroom(): HasMany
    {
        return $this->hasMany(Homeroom::class, 'section', 'section');
    }

    public function students(): HasMany
    {
        return $this->hasMany(StudentSection::class, 'section', 'section');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'section_or_strand', 'section');
    }

    public function subjectAssignments(): HasMany
    {
        return $this->hasMany(SubjectUser::class, 'section', 'section');
    }

    // Helpers
    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year', $schoolyear);
    }

    public function scopeByGradeLevel($query, string $gradelevel)
    {
        return $query->where('gradelevel', $gradelevel);
    }
}
