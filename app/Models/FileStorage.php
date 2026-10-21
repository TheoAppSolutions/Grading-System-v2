<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

#[Table('file_storages')]
#[Unguarded]
class FileStorage extends Model
{
    // Relationships
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helpers
    public function scopeByType($query, string $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    public function scopeByOwner($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function getStoragePathAttribute(): ?string
    {
        return $this->hashed_filename
            ? Storage::disk('public')->path($this->hashed_filename)
            : null;
    }
}
