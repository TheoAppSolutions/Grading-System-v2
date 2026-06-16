<?php

namespace App\Models\Config;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

class SubjectCategory extends Model
{
    use Sushi;

    protected function getRows(): array
    {
        return match (config('app.name')) {
            'BIS' => $this->getRowsBIS(),
            'HCGS' => $this->getRowsHCGS(),
            default => []
        };
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'category_id', 'id');
    }

    public function getRowsBIS()
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

    public function getRowsHCGS()
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
            ['id' => 11, 'label' => 'Physical Education',    'category' => 'general'],
            ['id' => 12, 'label' => 'The Arts',              'category' => 'general'],
            ['id' => 13, 'label' => 'Music',                 'category' => 'general'],
            ['id' => 14, 'label' => 'Agriculture',           'category' => 'general'],
            ['id' => 15, 'label' => 'History',               'category' => 'general'],
            ['id' => 16, 'label' => 'Civics/Geography',      'category' => 'general'],
            ['id' => 17, 'label' => 'Business Studies',      'category' => 'general'],
            ['id' => 18, 'label' => 'Social Science',        'category' => 'general'],
        ];
    }
}
