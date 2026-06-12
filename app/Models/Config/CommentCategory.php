<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class CommentCategory extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 1,
            'label' => 'Academic Performance',
        ],
        [
            'id' => 2,
            'label' => 'Attitude',
        ],
        [
            'id' => 3,
            'label' => 'Conduct',
        ],
        [
            'id' => 4,
            'label' => 'Needing Improvement',
        ],
        [
            'id' => 5,
            'label' => 'Attendance and Tardy',
        ],
        [
            'id' => 6,
            'label' => 'Overall Request',
        ],
    ];
}
