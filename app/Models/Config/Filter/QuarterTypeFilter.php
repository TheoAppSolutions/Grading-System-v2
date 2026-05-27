<?php

namespace App\Models\Config\Filter;

use Illuminate\Database\Eloquent\Model;

class QuarterTypeFilter extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'label' => 'Mid Quarter',
        ],
        [
            'id' => 2,
            'label' => 'Quarter',
        ],
    ];
}
