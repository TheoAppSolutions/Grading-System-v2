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
    ];
}
