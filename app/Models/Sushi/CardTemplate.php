<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;

class CardTemplate extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'category' => 'Primary',
            'template' => 'A',
        ],
        [
            'id' => 2,
            'category' => 'Secondary',
            'template' => 'B',
        ],
        [
            'id' => 3,
            'category' => 'SHS',
            'template' => 'C',
        ],
    ];
}
