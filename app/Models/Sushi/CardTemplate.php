<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;

class CardTemplate extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'template' => 'A',
        ],
        [
            'id' => 2,
            'template' => 'B',
        ],
    ];
}
