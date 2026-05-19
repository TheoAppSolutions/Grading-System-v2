<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectCategory extends Model
{
    use \Sushi\Sushi;

    protected function getRows(): array
    {
        return [
            ['id' => 1, 'label' => 'English'],
            ['id' => 2, 'label' => 'Filipino'],
            ['id' => 3, 'label' => 'Mathematics'],
            ['id' => 4, 'label' => 'Science'],
            ['id' => 5, 'label' => 'Araling Panlipunan'],
            ['id' => 7, 'label' => 'MAPEH'],
            ['id' => 8, 'label' => 'TLE'],
        ];
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'category_id', 'id');
    }
}
