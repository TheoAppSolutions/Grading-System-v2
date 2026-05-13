<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('announcements')]
class Announcement extends Model
{
    // Relationships
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AnnouncementNotification::class);
    }

    // Helpers
    public function isActive(): bool
    {
        return now()->between($this->date_start, $this->date_end);
    }

    public function unreadRecipients(): HasMany
    {
        return $this->recipients()->where('is_read', false);
    }
}
