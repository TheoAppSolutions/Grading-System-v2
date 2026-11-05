<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class RestoredReportViewsTest extends TestCase
{
    public function test_attendance_report_renders_period_totals_and_student_rows(): void
    {
        $html = View::make('reports.templates.attendance', [
            'schoolYear' => new SchoolYear(['label' => '2026-2027']),
            'gradeLevel' => (object) ['label' => '7th Grade'],
            'entryLabel' => 'First Quarter',
            'schoolDays' => 50,
            'rows' => new Collection([[
                'student' => new Student(['last_name' => 'Lee', 'first_name' => 'Sam']),
                'section' => 'A',
                'present' => 48,
                'absent' => 2,
                'tardy' => 1,
            ]]),
        ])->render();

        $this->assertStringContainsString('Attendance Report', $html);
        $this->assertStringContainsString('First Quarter · School days: 50.0', $html);
        $this->assertStringContainsString('Lee, Sam', $html);
        $this->assertStringContainsString('<td>48.0</td>', $html);
    }
}
