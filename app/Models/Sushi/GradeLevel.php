<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeLevel extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'label' => '1st Grade',
        ],
        [
            'id' => 2,
            'label' => '2nd Grade',
        ],
        [
            'id' => 3,
            'label' => '3rd Grade',
        ],
        [
            'id' => 4,
            'label' => '4th Grade',
        ],
        [
            'id' => 5,
            'label' => '5th Grade',
        ],
        [
            'id' => 6,
            'label' => '6th Grade',
        ],
        [
            'id' => 7,
            'label' => '7th Grade',
        ],
        [
            'id' => 8,
            'label' => '8th Grade',
        ],
        [
            'id' => 9,
            'label' => '9th Grade',
        ],
        [
            'id' => 10,
            'label' => '10th Grade',
        ],
        [
            'id' => 11,
            'label' => '11th Grade',
        ],
        [
            'id' => 12,
            'label' => '12th Grade',
        ],
    ];

    // Relationships
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'grade_level', 'id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'grade_level', 'id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'grade_level', 'id');
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
