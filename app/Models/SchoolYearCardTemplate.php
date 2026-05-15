<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;

#[Table('school_year_card_templates', timestamps: false)]
#[Unguarded]
class SchoolYearCardTemplate extends Model
{
    // Relationships
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'id');
    }
}
