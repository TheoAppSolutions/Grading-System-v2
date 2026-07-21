<?php

namespace App\Models\Config;

use App\Helpers\MyTools;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

class GradeLevel extends Model
{
    use Sushi;

    protected $rows = [
        ['id' => 1, 'sort_order' => 3, 'label' => '1st Grade', 'category' => 'primary'],
        ['id' => 2, 'sort_order' => 4, 'label' => '2nd Grade', 'category' => 'primary'],
        ['id' => 3, 'sort_order' => 5, 'label' => '3rd Grade', 'category' => 'primary'],
        ['id' => 4, 'sort_order' => 6, 'label' => '4th Grade', 'category' => 'primary'],
        ['id' => 5, 'sort_order' => 7, 'label' => '5th Grade', 'category' => 'primary'],
        ['id' => 6, 'sort_order' => 8, 'label' => '6th Grade', 'category' => 'primary'],
        ['id' => 7, 'sort_order' => 9, 'label' => '7th Grade', 'category' => 'secondary'],
        ['id' => 8, 'sort_order' => 10, 'label' => '8th Grade', 'category' => 'secondary'],
        ['id' => 9, 'sort_order' => 11, 'label' => '9th Grade', 'category' => 'secondary'],
        ['id' => 10, 'sort_order' => 12, 'label' => '10th Grade', 'category' => 'secondary'],
    ];

    public function getRows(): array
    {
        $rows = $this->rows;

        return MyTools::handleExtraGrades($rows);
    }
}
