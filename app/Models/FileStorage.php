<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('file_storages')]
class FileStorage extends Model
{
    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function scopeByType($query, string $filetype)
    {
        return $query->where('filetype', $filetype);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getStoragePathAttribute(): string
    {
        return storage_path("app/uploads/{$this->hashedfilename}");
    }
}
