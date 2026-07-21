<?php

namespace App\Helpers;

// use App\Helpers\BIS\MigrationHelpers as Tools;
use App\Helpers\HCGS\MigrationHelpers as Tools;

class MigrationTools extends Tools
{
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
            ['code' => 'OR', 'comment_category_id' => 6, 'type' => 1, 'description' => 'A Parent-Teacher Conference is requested ASAP'],
        ];
    }

    public static function cleanEncoding(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
        }

        return $value;
    }
}
