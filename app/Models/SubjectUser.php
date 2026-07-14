<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('subject_users')]
#[Unguarded]
class SubjectUser extends Model
{
    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
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
        return $this->hasMany(Grade::class, 'subject_id', 'id');
    }

    // Helpers
    public function hasCoTeacher(): bool
    {
        return !is_null($this->user2_id);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'school_year_id');
    }
}
