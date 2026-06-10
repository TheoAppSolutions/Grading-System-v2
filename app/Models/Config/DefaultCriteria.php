<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class DefaultCriteria extends Model
{
    use \Sushi\Sushi;

    protected array $rows = [
        [
            'id' => 1,
            'category' => 'Academic Performance',
            'description' => 'Grasps new ideas readily and making steady progress',
        ],
        [
            'id' => 2,
            'category' => 'Academic Performance',
            'description' => 'Keep up the great work!',
        ],[
            'id' => 3,
            'category' => 'Academic Performance',
            'description' => 'Inclined to hurry too much. This affects quality of work.',
        ],[
            'id' => 4,
            'category' => 'Academic Performance',
            'description' => 'Low grade affects his/her general average',
        ],[
            'id' => 5,
            'category' => 'Academic Performance',
            'description' => 'Has the ability but is not applying himself/herself',
        ],[
            'id' => 6,
            'category' => 'Attitude',
            'description' => 'Is a steadfast, conscientious worker',
        ],[
            'id' => 7,
            'category' => 'Attitude',
            'description' => 'Performs well in everything he/she undertakes',
        ],[
            'id' => 8,
            'category' => 'Attitude',
            'description' => 'Does not volunteer often but is willing to take part in classwork when requested',
        ],[
            'id' => 9,
            'category' => 'Attitude',
            'description' => 'Is learning to share his/her ideas',
        ],[
            'id' => 10,
            'category' => 'Conduct',
            'description' => 'Works well in groups, planning and carrying out activities',
        ],[
            'id' => 11,
            'category' => 'Conduct',
            'description' => 'Respects the authority of those in charge of various',
        ],[
            'id' => 12,
            'category' => 'Conduct',
            'description' => 'Needs to listen and follow directions; has difficulty in concentrating',
        ],[
            'id' => 13,
            'category' => 'Conduct',
            'description' => 'Needs to improve in self-control but seems unconcerned about it',
        ],[
            'id' => 14,
            'category' => 'Needing Improvement',
            'description' => 'Does little work in school unless given full attention by the teacher',
        ],[
            'id' => 15,
            'category' => 'Needing Improvement',
            'description' => 'Not doing school work and seems not to care about doing follow up is needed at home.',
        ],[
            'id' => 16,
            'category' => 'Needing Improvement',
            'description' => 'Needs to give full attention/concentration in all subject areas',
        ],[
            'id' => 17,
            'category' => 'Needing Improvement',
            'description' => 'Language/Reading handicap is the main source of difficulty.',
        ],[
            'id' => 18,
            'category' => 'Attendance and Tardy',
            'description' => 'Does good work when present but frequent absences cause difficulties.',
        ],[
            'id' => 19,
            'category' => 'Attendance and Tardy',
            'description' => 'Frequent absences affect the quality of work and assignments.',
        ],[
            'id' => 20,
            'category' => 'Attendance and Tardy',
            'description' => 'Is tardy frequently. This disturbs classmates and also affects his/her work.',
        ],[
            'id' => 21,
            'category' => 'Overall Request',
            'description' => 'A Parent-Teacher Conference is requested ASAP',
        ],
    ];
}
