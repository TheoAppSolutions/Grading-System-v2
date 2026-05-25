<?php

use App\Models;
use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Resources\School\SchoolYears\SchoolYearResource;
use Filament\Notifications\Notification;

class MyTools
{
    public static function systemLogo(): string
    {
        return url('images/logo.png?' . time());
    }

    public static function getMonths(): array
    {
        return [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    public static function getSchoolYear(): string
    {
        if (isset($_GET['school_year'])) {
            return (string) $_GET['school_year'];
        }

        $schoolYear = Models\SchoolYear::where('active', 1)
            ->select([
                'id',
                'school_year'
            ])
            ->first();

        if ($schoolYear === null) {
            Notification::make()
                ->title('No active school year found.')
                ->warning()
                ->send();

            return false;
        }

        return $schoolYear;
    }

    public static function ordinal(int $number): string
    {
        $abs = abs($number);
        $mod100 = $abs % 100;
        $mod10  = $abs % 10;

        $suffix = match (true) {
            $mod100 >= 11 && $mod100 <= 13 => 'th',
            $mod10 === 1                   => 'st',
            $mod10 === 2                   => 'nd',
            $mod10 === 3                   => 'rd',
            default                        => 'th',
        };

        return $number . $suffix;
    }

    public static function getImage(int|string $id = '', string $type = 'users'): string
    {
        $relativePath = match ($type) {
            'users'    => 'images/users/' . ($id !== '' ? $id : Auth::id()) . '.jpg',
            'students' => 'images/students/' . $id . '.jpg',
            default    => '',
        };

        if ($relativePath === '' || !file_exists(public_path($relativePath))) {
            return self::systemLogo();
        }

        return url($relativePath . '?' . time());
    }

    public static function getStudentFullname(int|string $studentId): string
    {
        $student = Models\Student::findOrFail($studentId);

        return trim("{$student->firstname} {$student->lastname}");
    }

    public static function checkMark(): string
    {
        $src = e(url('checkmark.png'));
        return "<img src=\"{$src}\" style=\"height:20px;width:20px;\" alt=\"✓\">";
    }

    public static function cleanString(string $string): string
    {
        return trim(preg_replace('/[^A-Za-z0-9\-]/', '', $string) ?? '');
    }
}
