<?php

return [
    'sections' => env('FEATURE_SECTION', false),
    'grade_import' => env('FEATURE_GRADE_IMPORT', false),
    'card_template' => env('FEATURE_CARD_TEMPLATE', false),
    'homeroom_deportment' => env('FEATURE_HOMEROOM_DEPORTMENT', false),
    'student_lrn' => env('FEATURE_STUDENT_LRN', false),
    'midquarter' => env('FEATURE_MIDQUARTER', false),
    'house_team' => [
        'enabled' => env('FEATURE_HOUSE_TEAM', false),
        'options' => [
            1 => 'Jacob',
            2 => 'Esaw'
        ]
    ],
    'extended_grade' => [
        11 => match (config('app.name')) {
            'HCGS' => 'Kindy',
            default => 11
        },
        12 => match (config('app.name')) {
            'HCGS' => 'Prep',
            default => 12
        },
    ],
];
