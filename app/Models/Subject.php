<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('subjects')]
#[Unguarded]
class Subject extends Model
{
    // Relationships
    public function subjectUnit(): HasMany
    {
        return $this->hasMany(SubjectUnit::class, 'subject_id', 'id');
    }

    public function subjectUser(): HasMany
    {
        return $this->hasMany(SubjectUser::class, 'subject_id', 'id');
    }

    public function subjectExcluded(): HasMany
    {
        return $this->hasMany(SubjectExcluded::class, 'subject_id', 'id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'subject_id', 'id');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class, 'subject_id', 'id');
    }

    public function midquarterComments(): HasMany
    {
        return $this->hasMany(CommentMidquarter::class, 'subject_id', 'id');
    }

    // Configs
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(Config\GradeLevel::class, 'grade_level_id', 'id');
    }

    public function subjectCategory(): BelongsTo
    {
        return $this->belongsTo(Config\SubjectCategory::class, 'subject_title', 'label');
    }

    // Helpers
    public function getUnitsByYear(string $schoolyear): ?SubjectUnit
    {
        return $this->subjectUnit()->where('school_year_id', $schoolyear)->first();
    }

    public function getTeachersByYear(string $schoolyear): HasMany
    {
        return $this->subjectUser()->where('school_year_id', $schoolyear);
    }
}
