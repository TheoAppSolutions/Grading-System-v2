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

    public static function default_criteria()
    {
        return [
            ['code' => 'AP1', 'comment_category_id' => 1, 'type' => 1, 'description' => 'Grasps new ideas readily and making steady progress'],
            ['code' => 'AP2', 'comment_category_id' => 1, 'type' => 1, 'description' => 'Keep up the great work!'],
            ['code' => 'AP3', 'comment_category_id' => 1, 'type' => 1, 'description' => 'Inclined to hurry too much. This affects quality of work.'],
            ['code' => 'AP4', 'comment_category_id' => 1, 'type' => 1, 'description' => 'Low grade affects his/her general average'],
            ['code' => 'AP5', 'comment_category_id' => 1, 'type' => 1, 'description' => 'Has the ability but is not applying himself/herself'],
            ['code' => 'A1', 'comment_category_id' => 2, 'type' => 1, 'description' => 'Is a steadfast, conscientious worker'],
            ['code' => 'A2', 'comment_category_id' => 2, 'type' => 1, 'description' => 'Performs well in everything he/she undertakes'],
            ['code' => 'A3', 'comment_category_id' => 2, 'type' => 1, 'description' => 'Does not volunteer often but is willing to take part in classwork when requested'],
            ['code' => 'A4', 'comment_category_id' => 2, 'type' => 1, 'description' => 'Is learning to share his/her ideas'],
            ['code' => 'C1', 'comment_category_id' => 3, 'type' => 1, 'description' => 'Works well in groups, planning and carrying out activities'],
            ['code' => 'C2', 'comment_category_id' => 3, 'type' => 1, 'description' => 'Respects the authority of those in charge of various'],
            ['code' => 'C3', 'comment_category_id' => 3, 'type' => 1, 'description' => 'Needs to listen and follow directions; has difficulty in concentrating'],
            ['code' => 'C4', 'comment_category_id' => 3, 'type' => 1, 'description' => 'Needs to improve in self-control but seems unconcerned about it'],
            ['code' => 'NI1', 'comment_category_id' => 4, 'type' => 1, 'description' => 'Does little work in school unless given full attention by the teacher'],
            ['code' => 'NI2', 'comment_category_id' => 4, 'type' => 1, 'description' => 'Not doing school work and seems not to care about doing follow up is needed at home.'],
            ['code' => 'NI3', 'comment_category_id' => 4, 'type' => 1, 'description' => 'Needs to give full attention/concentration in all subject areas'],
            ['code' => 'NI4', 'comment_category_id' => 4, 'type' => 1, 'description' => 'Language/Reading handicap is the main source of difficulty.'],
            ['code' => 'AT1', 'comment_category_id' => 5, 'type' => 1, 'description' => 'Does good work when present but frequent absences cause difficulties.'],
            ['code' => 'AT2', 'comment_category_id' => 5, 'type' => 1, 'description' => 'Frequent absences affect the quality of work and assignments.'],
            ['code' => 'AT3', 'comment_category_id' => 5, 'type' => 1, 'description' => 'Is tardy frequently. This disturbs classmates and also affects his/her work.'],
            ['code' => 'OR', 'comment_category_id' => 6, 'type' => 1, 'description' => 'A Parent-Teacher Conference is requested ASAP']
        ];
    }
}
