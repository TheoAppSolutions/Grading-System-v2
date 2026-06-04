<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Migration extends Model
{
    use Sushi;

    protected array $rows = [
        // ['step' => '1', 'description' => 'Users', 'old_table' => 'users', 'new_table' => 'users'],
        ['step' => '1', 'description' => 'Students',      'old_table' => 'students',       'new_table' => 'students',      'additional' => null],
        ['step' => '1', 'description' => 'Subjects',      'old_table' => 'subjects',       'new_table' => 'subjects',      'additional' => 'insertChineseSubject'],
        ['step' => '1', 'description' => 'School Years',  'old_table' => 'schoolyear',     'new_table' => 'school_years',  'additional' => null],
        ['step' => '2', 'description' => 'Sections',      'old_table' => 'sections',       'new_table' => 'sections',      'additional' => null],
        ['step' => '2', 'description' => 'Subject Users', 'old_table' => 'subjects_user',  'new_table' => 'subject_users', 'additional' => null],
        ['step' => '2', 'description' => 'Subject Units', 'old_table' => 'subjects_units', 'new_table' => 'subject_units', 'additional' => null],
        ['step' => '3', 'description' => 'Enrollments',   'old_table' => 'enrollment',     'new_table' => 'enrollments',   'additional' => 'syncCurrentLevels'],
        ['step' => '4', 'description' => 'Grades 1',      'old_table' => 'grades_1',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 2',      'old_table' => 'grades_2',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 3',      'old_table' => 'grades_3',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 4',      'old_table' => 'grades_4',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 5',      'old_table' => 'grades_5',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 6',      'old_table' => 'grades_6',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 7',      'old_table' => 'grades_7',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 8',      'old_table' => 'grades_8',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 9',      'old_table' => 'grades_9',        'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 10',     'old_table' => 'grades_10',       'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 11',     'old_table' => 'grades_11',       'new_table' => 'grades',        'additional' => null],
        ['step' => '4', 'description' => 'Grades 12',     'old_table' => 'grades_12',       'new_table' => 'grades',        'additional' => null],
    ];
}
