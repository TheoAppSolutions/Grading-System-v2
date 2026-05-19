<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('subjects')]
#[Unguarded]
class Subject extends Model
{
    // Relationships
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'gradelevel', 'code');
    }

    public function units(): HasMany
    {
        return $this->hasMany(SubjectUnit::class, 'subject_id', 'subject_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(SubjectUser::class, 'subject_id', 'subject_id');
    }

    public function excluded(): HasMany
    {
        return $this->hasMany(SubjectExcluded::class, 'subject_id', 'subject_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'subject_id', 'subject_id');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class, 'subject_id', 'subject_id');
    }

    public function midquarterComments(): HasMany
    {
        return $this->hasMany(CommentMidquarter::class, 'subject_id', 'subject_id');
    }

    public function subjectCategory(): BelongsTo
    {
        return $this->belongsTo(SubjectCategory::class, 'category_id', 'id');
    }

    // Helpers
    public function getUnitsByYear(string $schoolyear): ?SubjectUnit
    {
        return $this->units()->where('school_year', $schoolyear)->first();
    }

    public function getTeachersByYear(string $schoolyear): HasMany
    {
        return $this->assignments()->where('school_year', $schoolyear);
    }
}
