<?php

namespace App\Models\Config;

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

    public static function getOptions(int $id): array
    {
        return match($id) {
            2  => [1 => '1st Semester', 2 => '2nd Semester'],
            4  => [1 => '1st Quarter', 2 => '2nd Quarter', 3 => '3rd Quarter', 4 => '4th Quarter'],
            10 => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ],
            default => [],
        };
    }
}
