<?php

namespace App\Helpers\HCGS;

class HelperTools
{
    public static function handleDuplicateUsers(array $user): ?array
    {
        // Duplicate issue - forcing to true
        if (in_array($user['id'], [6, 19, 7])) {
            $user['active'] = true;
        }

        // Duplicate issue - forcing to skip
        if (in_array($user['id'], [45, 47, 53])) {
            return null;
        }

        return $user;
    }

    public static function handleExtraGrades($rows): array
    {
        return array_merge($rows, [
            ['id' => 11, 'sort_order' => 1, 'label' => 'Kindy', 'category' => 'pre-primary'],
            ['id' => 12, 'sort_order' => 2, 'label' => 'Prep', 'category' => 'pre-primary'],
        ]);
    }

    public static function getSubjectRows(): array
    {
        return [
            ['id' => 0,  'label' => 'Uncategorized',         'category' => 'others'],

            // Primary naming
            ['id' => 1,  'label' => 'HASS',                  'category' => 'primary'],
            ['id' => 2,  'label' => 'Health',                'category' => 'primary'],
            ['id' => 3,  'label' => 'Making A Living',       'category' => 'primary'],

            // Secondary naming equivalents
            ['id' => 4,  'label' => 'Personal Development',  'category' => 'secondary'], // Health
            ['id' => 5,  'label' => 'Home Economics',        'category' => 'secondary'], // Making A Living

            // Common subjects
            ['id' => 6,  'label' => 'CRE',                   'category' => 'general'],
            ['id' => 7,  'label' => 'English',               'category' => 'general'],
            ['id' => 8,  'label' => 'ICT/Practical Skills',  'category' => 'general'],
            ['id' => 9,  'label' => 'Mathematics',           'category' => 'general'],
            ['id' => 10, 'label' => 'Science',               'category' => 'general'],
            ['id' => 11, 'label' => 'P.E.',                  'category' => 'general'],
            ['id' => 12, 'label' => 'Arts',                  'category' => 'general'],
            ['id' => 13, 'label' => 'Music',                 'category' => 'general'],
            ['id' => 14, 'label' => 'Agriculture',           'category' => 'general'],
            ['id' => 15, 'label' => 'History',               'category' => 'general'],
            ['id' => 16, 'label' => 'Civics/Geography',      'category' => 'general'],
            ['id' => 17, 'label' => 'Business Studies',      'category' => 'general'],
            ['id' => 18, 'label' => 'Social Science',        'category' => 'general'],
        ];
    }
}
