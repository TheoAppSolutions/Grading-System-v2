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
        $new_id = match (config('app.name')) {
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
                // 'MAT' => dd('MAT'),
                default => $old_id,
            },
            default => $old_id,
        };

        return $new_id;
    }
}
