<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Config\Role;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Saade\FilamentFacehash\Concerns\HasFacehashAvatar;
use Marcelodelgado\Announcements\Traits\HasAnnouncements;

#[Table('users')]
#[Fillable(['name', 'email', 'last_name', 'first_name', 'middle_name', 'suffix', 'honorifics', 'password', 'active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasFacehashAvatar, HasAnnouncements;

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

    public function fileStorage(): HasMany
    {
        return $this->hasMany(FileStorage::class, 'owner_id', 'id');
    }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return trim("{$this->honorifics} {$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}");
    }

    public function isActive(): bool
    {
        return $this->active === true;
    }

    public static function getActiveUsers()
    {
        return self::where('active', true)
            ->where('id', '!=', 1)
            ->orderBy('name')
            ->get();
    }

    public function hasRole(int $roleId): bool
    {
        return $this->userRoles
            ->contains(fn($userRole) => $userRole->role_id === $roleId);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(1);
    }
    public function isPrincipal(): bool
    {
        return $this->hasRole(2) || $this->hasRole(1);
    }
    public function isOffice(): bool
    {
        return $this->hasRole(3) || $this->hasRole(1);
    }
    public function isHomeroom(): bool
    {
        return $this->hasRole(4) || $this->hasRole(1);
    }
    public function isTeacher(): bool
    {
        return $this->hasRole(5) || $this->hasRole(1);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            default => true,
        };
    }
}
