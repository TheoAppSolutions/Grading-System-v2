<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('student_fields')]
class StudentField extends Model
{
    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Helpers
    public static function getField(int $studentId, string $fieldName): ?string
    {
        return static::where('student_id', $studentId)
            ->where('field_name', $fieldName)
            ->value('field_value');
    }

    public static function setField(int $studentId, string $fieldName, string $value): void
    {
        static::updateOrCreate(
            ['student_id' => $studentId, 'field_name' => $fieldName],
            ['field_value' => $value]
        );
    }
}
