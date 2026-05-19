<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class CustomFields extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'field_name' => 'Custom Field 1',
        ],
        [
            'id' => 2,
            'field_name' => 'Custom Field 2',
        ],
    ];
}
