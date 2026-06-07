<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

class GradeLevel extends Model
{
    use Sushi;

    protected $rows = [
        ['id' => 1, 'sort_order' => 1, 'label' => '1st Grade', 'category' => 'primary'],
        ['id' => 2, 'sort_order' => 2, 'label' => '2nd Grade', 'category' => 'primary'],
        ['id' => 3, 'sort_order' => 3, 'label' => '3rd Grade', 'category' => 'primary'],
        ['id' => 4, 'sort_order' => 4, 'label' => '4th Grade', 'category' => 'primary'],
        ['id' => 5, 'sort_order' => 5, 'label' => '5th Grade', 'category' => 'primary'],
        ['id' => 6, 'sort_order' => 6, 'label' => '6th Grade', 'category' => 'primary'],
        ['id' => 7, 'sort_order' => 7, 'label' => '7th Grade', 'category' => 'secondary'],
        ['id' => 8, 'sort_order' => 8, 'label' => '8th Grade', 'category' => 'secondary'],
        ['id' => 9, 'sort_order' => 9, 'label' => '9th Grade', 'category' => 'secondary'],
        ['id' => 10, 'sort_order' => 10, 'label' => '10th Grade', 'category' => 'secondary'],

        ['id' => 11, 'sort_order' => 10, 'label' => 'Kindy', 'category' => 'prep'],
        ['id' => 12, 'sort_order' => 10, 'label' => 'Prep', 'category' => 'prep'],
        // ['id' => 11, 'sort_order' => 11, 'label' => '11th Grade', 'category' => 'shs'], // BIS only
        // ['id' => 12, 'sort_order' => 12, 'label' => '12th Grade', 'category' => 'shs'], // BIS only
    ];

    // Relationships
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'grade_level_id', 'id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'grade_level_id', 'id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'grade_level_id', 'id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'grade_level_id', 'id');
    }

    // Helpers
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
