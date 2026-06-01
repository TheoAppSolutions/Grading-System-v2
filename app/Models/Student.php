<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('students')]
#[Unguarded]
class Student extends Model
{
    public $incrementing = false;

    // Relationships
    public function info(): HasOne
    {
        return $this->hasOne(StudentInfo::class, 'student_id', 'id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(StudentField::class, 'student_id', 'id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id', 'id');
    }

    public function enrollmentLatest(): HasOne
    {
        return $this->hasOne(Enrollment::class)->latestOfMany();
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id', 'id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }

    public function deportments(): HasMany
    {
        return $this->hasMany(Deportment::class, 'student_id', 'id');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class, 'student_id', 'id');
    }

    public function graduate(): HasOne
    {
        return $this->hasOne(Graduate::class, 'student_id', 'id');
    }

    public function excludedSubjects(): HasMany
    {
        return $this->hasMany(SubjectExcluded::class, 'student_id', 'id');
    }

    public function midquarterComments(): HasMany
    {
        return $this->hasMany(CommentMidquarter::class, 'student_id', 'id');
    }

    public function quarterComments(): HasMany
    {
        return $this->hasMany(CommentQuarter::class, 'student_id', 'id');
    }

    // Helpers
    public function full_name(): string
    {
        return trim("{$this->lastname}, {$this->firstname} {$this->middlename} {$this->suffix}");
    }

    public function isActive(): bool
    {
        return $this->active === true;
    }

    public function isGraduated(): bool
    {
        return $this->graduate()->exists();
    }

    public function getCurrentEnrollment(): ?Enrollment
    {
        return $this->enrollments()->latest('entry_id')->first();
    }

    public function getGradesByYear(string $schoolyear): HasMany
    {
        return $this->grades()->where('school_year_id', $schoolyear);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }
}
