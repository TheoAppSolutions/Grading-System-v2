<?php

namespace App\Models\Config;

use App\Helpers\MyTools;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

class SubjectCategory extends Model
{
    use Sushi;

    protected function getRows(): array
    {
        return MyTools::getSubjectRows();
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'category_id', 'id');
    }
}
