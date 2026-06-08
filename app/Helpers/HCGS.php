<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class HCGS
{
    public static function isHCGS(): bool
    {
        return config('app.name') === 'HCGS';
    }

    public static function subjectIdChecker(object $row): bool
    {
        if (in_array($row->subject_id, ['MATH3', 'ENG', 'MAT', 'SCI', 'PE', 'ARTS', 'MATH'])) { // These subjects have no matching entries in new_db.subjects, so skip them
            Log::channel('migrationlog')->info('Skipping subject_user with subject_id '.$row->subject_id.' (id: '.$row->id.')');

            return false;
        }

        return true;
    }

    public static function gradeLevelChecker($gradeLevel): int
    {
        $mapping = [
            'Kindy' => 11,
            'Prep' => 12,
        ];

        return $mapping[$gradeLevel] ?? $gradeLevel; // Return the original grade level if it's not in the mapping
    }

    public static function userIdChecker($row): bool
    {
        if (in_array($row->user_id, [45, 47, 53])) {
            Log::channel('migrationlog')->info('Skipping subject_user with user_id '.$row->user_id.' (id: '.$row->id.')');
           
            return false;
        }

        return true;
    }
}
