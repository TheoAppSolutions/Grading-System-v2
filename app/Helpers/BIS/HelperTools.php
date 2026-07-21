<?php

namespace App\Helpers\BIS;

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
            ['id' => 11, 'sort_order' => 13, 'label' => '11th Grade', 'category' => 'shs'],
            ['id' => 12, 'sort_order' => 14, 'label' => '12th Grade', 'category' => 'shs'],
        ]);
    }

    public static function getSubjectRows()
    {
        return [
            ['id' => 0,  'label' => 'Uncategorized',         'category' => 'others'],

            // Primary (Grade 1–6)
            ['id' => 1,  'label' => 'Language',              'category' => 'primary'],
            ['id' => 2,  'label' => 'Reading',               'category' => 'primary'],

            // Secondary (Grade 7–10)
            ['id' => 3,  'label' => 'English',               'category' => 'secondary'],

            // General Subjects for Primary and Secondary
            ['id' => 4,  'label' => 'Filipino',              'category' => 'general'],
            ['id' => 5,  'label' => 'Mathematics',           'category' => 'general'],
            ['id' => 6,  'label' => 'Science',               'category' => 'general'],
            ['id' => 7,  'label' => 'Christian Formation',   'category' => 'general'],
            ['id' => 8,  'label' => 'Physical Education',    'category' => 'general'],
            ['id' => 9,  'label' => 'Art',                   'category' => 'general'],
            ['id' => 10, 'label' => 'Music',                 'category' => 'general'],
            ['id' => 11, 'label' => 'Computer',              'category' => 'general'],
            ['id' => 12, 'label' => 'Civics and Culture',    'category' => 'general'],
            ['id' => 13, 'label' => 'Livelihood Education',  'category' => 'general'],
            ['id' => 14, 'label' => 'H.E.L.E',               'category' => 'general'],
            ['id' => 15, 'label' => 'Chinese',               'category' => 'general'],

            // Senior High (Grade 11–12)
            ['id' => 16, 'label' => 'General Subjects',      'category' => 'shs'],
            ['id' => 17, 'label' => 'Applied Subjects',      'category' => 'shs'],
            ['id' => 18, 'label' => 'Specialized Subjects',  'category' => 'shs'],
        ];
    }
}