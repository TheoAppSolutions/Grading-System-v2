<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class CardTemplate extends Model
{
    use Sushi;

    protected $rows = [
        [
            'id' => 1,
            'category' => 'Primary',
            'remarks' => 'Default',
        ],
        [
            'id' => 2,
            'category' => 'Secondary',
            'remarks' => 'Default',
        ],
        [
            'id' => 3,
            'category' => 'SHS',
            'remarks' => 'Default',
        ],
    ];

    public static function getOptions(int $id): array
    {
        return match ($id) {
            2 => [1 => '1st Semester', 2 => '2nd Semester'],
            4 => [1 => '1st Quarter', 2 => '2nd Quarter', 3 => '3rd Quarter', 4 => '4th Quarter'],
            10 => [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ],
            default => [],
        };
    }

    public static function getOptionsLabel(int $id): string
    {
        return match ($id) {
            2 => "Semester",
            default => "Quarter",
        };
    }

    public static function getCategoriesOptions(): array
    {
        return [
            2 => 'By Semester (2 entries)',
            4 => 'By Quarter (4 entries)',
            10 => 'By Month (10 entries)',
        ];
    }
}
