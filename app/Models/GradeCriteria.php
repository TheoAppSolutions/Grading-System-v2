<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('grade_criteria')]
#[Unguarded]
class GradeCriteria extends Model
{
    // Relationships
    public function midquarterScores(): HasMany
    {
        return $this->hasMany(CommentMidquarter::class, 'criteria_id');
    }

    public function quarterScores(): HasMany
    {
        return $this->hasMany(CommentQuarter::class, 'criteria_id');
    }

    // Helpers
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function getMidquarterCriteria()
    {
        return static::active()
            ->byType('midquarter')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public static function getQuarterCriteria()
    {
        return static::active()
            ->byType('quarter')
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}
