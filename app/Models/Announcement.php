<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Marcelodelgado\Announcements\Models\Announcement as PackageAnnouncement;

class Announcement extends PackageAnnouncement
{
    protected $fillable = [
        'title',
        'body',
        'type',
        'target_type',
        'target_user_id',
        'is_active',
        'is_dismissible',
        'starts_at',
        'expires_at',
    ];

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function scopeTargetedTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $targetTypes = [];

        if ($user->isTeacher()) {
            $targetTypes[] = 'teachers';
        }

        if ($user->isOffice()) {
            $targetTypes[] = 'office';
        }

        if ($user->isHomeroom()) {
            $targetTypes[] = 'homeroom';
        }

        return $query->where(function (Builder $query) use ($user, $targetTypes): void {
            $query->where('target_type', 'all');

            if ($targetTypes !== []) {
                $query->orWhereIn('target_type', $targetTypes);
            }

            $query->orWhere(fn (Builder $query): Builder => $query
                ->whereIn('target_type', ['users', 'specific_teacher'])
                ->where('target_user_id', $user->getKey()));
        });
    }
}
