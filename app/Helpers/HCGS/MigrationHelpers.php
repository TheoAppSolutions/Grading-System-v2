<?php

namespace App\Helpers\HCGS;

class MigrationHelpers
{
    public static function replaceUserId(int $old_id): ?int
    {
        $new_id = match (config('app.name')) {
            'HCGS' => match ($old_id) {
                2 => 1,
                45 => 6,
                47 => 19,
                53 => 7,
                default => null,
            },
            default => null,
        };

        return $new_id;
    }

    public static function replaceSubjectId(string $old_id): ?string
    {
        return match (config('app.name')) {
            'HCGS' => match ($old_id) {
                'MATH3' => 'MATH03',
                'MATH' => 'MATHJR',
                'MAT' => 'MATHPR',
                'SCI' => 'SCIEPR',
                'ENG' => 'ENGLPR',
                'CRE' => 'CREPR', // CREPR
                'ARTS' => 'ARTSPR',
                'PE' => 'PEPR',
                'SOCSCI7' => 'SOCSCI07',
                'SOCSCI8' => 'SOCSCI08',
                'SOCSCI9' => 'SOCSCI09',
                default => $old_id,
            },
            default => $old_id,
        };
    }

    public static function skipSubjectId(string $subject_id): bool
    {
        return match (config('app.name')) {
            'HCGS' => in_array($subject_id, ['MATH3', 'MATH', 'MAT', 'SCI', 'ENG', 'CRE', 'ARTS', 'PE']),
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
            'HCGS' => [
                ['subject_code' => 'CIVI07', 'subject_title' => 'Civics/Geography', 'subject_category_id' => 0, 'grade_level_id' => 7],
            ],
            default => [],
        };
    }

    public static function last_grade_level_id(): int
    {
        return 10; // 10th Grade
    }
}
