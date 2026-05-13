<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('logs')]
#[Unguarded]
class Log extends Model
{
    // Relationships
    // user_id is varchar in original schema so no FK constraint,
    // but we can still define the relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helpers
    public static function record(int|string $userId, string $info, string $description): void
    {
        static::create([
            'user_id'     => $userId,
            'info'        => $info,
            'description' => $description,
        ]);
    }

    public function scopeByUser($query, int|string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByInfo($query, string $info)
    {
        return $query->where('info', $info);
    }
}
