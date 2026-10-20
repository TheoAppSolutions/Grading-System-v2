<?php

namespace App\Models;

use App\Models\Config\CommentCategory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('comments_criteria')]
#[Unguarded]
class CommentCriteria extends Model
{
    protected $casts = [
        'active' => 'boolean',
        'type' => 'integer',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommentCategory::class, 'comment_category_id');
    }

    public function midquarterScores(): HasMany
    {
        return $this->hasMany(Comment::class, 'criteria_id');
    }

    public function quarterScores(): HasMany
    {
        return $this->hasMany(Comment::class, 'criteria_id');
    }

    // Helpers
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByType($query, int|string $type)
    {
        $value = match ($type) {
            'midquarter' => 1,
            'quarter' => 2,
            default => (int) $type,
        };

        return $query->where('type', $value);
    }

    public function scopeByCategory($query, int|string $category)
    {
        return $query->where('comment_category_id', $category);
    }

    public static function getMidquarterCriteria()
    {
        return static::active()
            ->byType(1)
            ->orderBy('code')
            ->get();
    }

    public static function getQuarterCriteria()
    {
        return static::active()
            ->byType(2)
            ->orderBy('code')
            ->get();
    }
}
