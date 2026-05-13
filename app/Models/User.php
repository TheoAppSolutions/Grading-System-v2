<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Table('users')]
#[Fillable(['name', 'last_name', 'first_name', 'middle_name', 'suffix', 'honorifics', 'password', 'active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    // Relationships
    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(SubjectUser::class);
    }

    public function homeroom(): HasOne
    {
        return $this->hasOne(Homeroom::class);
    }

    public function sentAnnouncements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'from_user_id');
    }

    public function receivedAnnouncements(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AnnouncementNotification::class, 'to_user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(FileStorage::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return trim("{$this->title} {$this->firstname} {$this->middlename} {$this->lastname} {$this->suffix}");
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role_description', $role)->exists();
    }

    public function isActive(): bool
    {
        return $this->active === true;
    }
}
