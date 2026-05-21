<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('school_years')]
#[Unguarded]
class SchoolYear extends Model
{
    // Relationships
    public function schoolYearInfo(): HasMany
    {
        return $this->hasMany(SchoolYearInfo::class, 'school_year_id', 'id');
    }

    public function schoolYearCardTemplate(): HasMany
    {
        return $this->hasMany(SchoolYearCardTemplate::class, 'school_year_id', 'id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'school_year', 'school_year');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'school_year', 'school_year');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'school_year', 'school_year');
    }
}
