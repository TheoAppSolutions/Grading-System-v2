<?php

namespace App\Models\Config\Filter;

use Illuminate\Database\Eloquent\Model;

class QuarterNumberFilter extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'label' => '1st Quarter',
        ],
        [
            'id' => 2,
            'label' => '2nd Quarter',
        ],
        [
            'id' => 3,
            'label' => '3rd Quarter',
        ],
        [
            'id' => 4,
            'label' => '4th Quarter',
        ],
    ];
}
