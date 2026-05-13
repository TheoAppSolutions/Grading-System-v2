<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('settings')]
class Setting extends Model
{
    // Helpers
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('field_name', $key)->value('field_value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['field_name' => $key],
            ['field_value' => $value]
        );
    }

    public static function forget(string $key): void
    {
        static::where('field_name', $key)->delete();
    }
}
