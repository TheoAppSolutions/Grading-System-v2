<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('grade_levels')]
#[Unguarded]
class GradeLevel extends Model
{
    // Relationships
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'gradelevel', 'code');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'gradelevel', 'code');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'gradelevel', 'code');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'grade_level', 'id');
    }

    // Helpers
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
