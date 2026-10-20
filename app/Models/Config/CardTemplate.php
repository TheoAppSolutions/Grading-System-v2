<?php

namespace App\Models\Config;

use App\Helpers\MyTools;
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
            10, 12 => MyTools::getMonths(),
            default => [],
        };
    }

    public static function getOptionsLabel(int $id): string
    {
        return match ($id) {
            2 => 'Semester',
            10, 12 => 'Month',
            default => 'Quarter',
        };
    }

    public static function getCategoriesOptions(): array
    {
        return [
            2 => 'By Semester (2 entries)',
            4 => 'By Quarter (4 entries)',
            10 => 'By Month (10 entries)',
            12 => 'By Month (12 entries)',
        ];
    }
}
