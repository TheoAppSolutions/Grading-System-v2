<?php

namespace App\Models\Config;

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
            'role_description' => 'Admin',
            'color_template' => 'info',
        ],
        [
            'id' => 2,
            'role_description' => 'Principal',
            'color_template' => 'primary',
        ],
        [
            'id' => 3,
            'role_description' => 'Office',
            'color_template' => 'info',
        ],
        [
            'id' => 4,
            'role_description' => 'Homeroom',
            'color_template' => 'warning',
        ],
        [
            'id' => 5,
            'role_description' => 'Teacher',
            'color_template' => 'success',
        ],
        [
            'id' => 6,
            'role_description' => 'Registrar',
            'color_template' => 'danger',
        ],
        [
            'id' => 7,
            'role_description' => 'Secretary',
            'color_template' => 'warning',
        ],
    ];

    // Relationships
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'role_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_roles', 'id', 'user_id');
    }
}
