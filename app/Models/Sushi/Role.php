<?php

namespace App\Models\Sushi;

use App\Models\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'name' => 'Admin',
        ],
        [
            'id' => 2,
            'name' => 'Office',
        ],
        [
            'id' => 3,
            'name' => 'Teacher',
        ],
    ];

    // Relationships
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'role_id', 'role_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_roles', 'role_id', 'user_id');
    }
}
