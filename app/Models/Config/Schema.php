<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Role extends Model
{
    use Sushi;

    protected $rows = [
        [
            'feature' => 'Section',
            'enabled' => true,
        ],
    ];
}
