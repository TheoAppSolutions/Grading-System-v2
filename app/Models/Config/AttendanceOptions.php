<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class AttendanceOptions extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
            'id' => 2,
            'options' => [
                1  => '1st Semester',
                2  => '2nd Semester',
            ]
        ],
        [
            'id' => 4,
            'options' => [
                1  => '1st Quarter',
                2  => '2nd Quarter',
                3  => '3rd Quarter',
                4  => '4th Quarter',
            ]
        ],
        [
            'id' => 10,
            'options' => [
                1  => 'January',
                2  => 'February',
                3  => 'March',
                4  => 'April',
                5  => 'May',
                6  => 'June',
                7  => 'July',
                8  => 'August',
                9  => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ]
        ],
    ];
}
