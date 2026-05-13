<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('subject_users')]
class SubjectUser extends Model
{
    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'subject_id', 'subject_id');
    }

    // Helpers
    public function hasCoTeacher(): bool
    {
        return !is_null($this->user2_id);
    }

    public function scopeBySchoolYear($query, string $schoolyear)
    {
        return $query->where('school_year', $schoolyear);
    }

    public function scopeBySection($query, string $section)
    {
        return $query->where('section', $section);
    }

    public function scopeBySemester($query, string $semester)
    {
        return $query->where('semester', $semester);
    }
}
