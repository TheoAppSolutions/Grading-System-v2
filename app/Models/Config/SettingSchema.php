<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class SettingSchema extends Model
{
    use Sushi;

    protected $rows = [
        [
            'feature' => 'Section',
            'enabled' => false,
        ],
        [
            'feature' => 'Subject Category',
            'enabled' => false,
        ],
    ];

    public static function isEnabled(string $feature): bool
    {
        return static::where('feature', $feature)->value('enabled') ?? false;
    }
}
