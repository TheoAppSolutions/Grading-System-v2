<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Config\GradeLevel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Table('enrollments')]
#[Unguarded]
class Enrollment extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id', 'id');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(Schoolyear::class, 'school_year_id', 'id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

    public function subjectUsers(): HasMany
    {
        return $this->hasMany(SubjectUser::class, 'school_year_id');
    }

    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            SubjectUser::class,
            'school_year_id', // FK on subject_user -> enrollments
            'id',             // FK on subjects
            'id',             // local key on enrollments
            'subject_id'      // local key on subject_user
        ); //->whereColumn('subjects.grade_level_id', 'enrollments.grade_level_id');
    }

    public function matchedSubjects()
    {
        return Subject::query()
            ->join('subject_users', 'subject_users.subject_id', '=', 'subjects.id')
            ->where('subject_users.school_year_id', $this->school_year_id)
            ->where('subjects.grade_level_id', $this->grade_level_id)
            ->select('subjects.*');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'id', 'student_id');
    }

    public static function isEnrolled(int $studentId, string $schoolyear): bool
    {
        return static::where('student_id', $studentId)
            ->where('school_year_id', $schoolyear)
            ->exists();
    }
}
