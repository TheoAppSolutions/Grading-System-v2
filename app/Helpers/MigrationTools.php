<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class MigrationTools
{
    public static function replaceUserId(int $old_id): ?int
    {
        $new_id = match (config('app.name')) {
            'BIS'  => match ($old_id) {
                188 => 114,
                default => null,
            },
            'HCGS' => match ($old_id) {
                2 => 1,
                45  => 6,
                47  => 19,
                53  => 7,
                default => null,
            },
            default => null,
        };

        return $new_id;
    }

    public static function replaceSubjectId(string $old_id): ?string
    {
        return match (config('app.name')) {
            'BIS'  => match ($old_id) {
                default => $old_id,
            },
            'HCGS' => match ($old_id) {
                'MATH3'  => 'MATH03',
                'MATH' => 'MATHJR',
                'MAT' => 'MATHPR',
                'SCI' => 'SCIEPR',
                'ENG' => 'ENGLPR',
                'CRE' => 'CREPR', //CREPR
                'ARTS' => 'ARTSPR',
                default => $old_id,
            },
            default => $old_id,
        };
    }

    public static function skipSubjectId(string $subject_id): bool
    {
        return match (config('app.name')) {
            'HCGS' => in_array($subject_id, ['MATH3', 'MATH', 'MAT', 'SCI', 'ENG', 'CRE', 'ARTS']),
            default => false
        };
    }

    public static function gradeLevelChecker($gradeLevel): int
    {
        return match (config('app.name')) {
            'HCGS' => match ($gradeLevel) {
                'Kindy' => 11,
                'Prep' => 12,
                default => $gradeLevel
            },
            default => $gradeLevel,
        };
    }

    public static function missingSubjects()
    {
        return match (config('app.name')) {
            'BIS' => [
                ['subject_code' => 'CHIN01', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 1],
                ['subject_code' => 'CHIN02', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 2],
                ['subject_code' => 'CHIN03', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 3],
                ['subject_code' => 'CHIN04', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 4],
                ['subject_code' => 'CHIN05', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 5],
                ['subject_code' => 'CHIN06', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 6],
                ['subject_code' => 'CHIN07', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 7],
                ['subject_code' => 'CHIN08', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 8],
                ['subject_code' => 'CHIN09', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 9],
                ['subject_code' => 'CHIN10', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 10],
            ],
            'HCGS' => [
                ['subject_code' => 'CIVI07', 'subject_title' => 'Civics/Geography', 'subject_category_id' => 0, 'grade_level_id' => 7],
            ],
            default => [],
        };
    }
}
