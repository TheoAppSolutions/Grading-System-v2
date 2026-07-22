<?php

namespace App\Http\Controllers;

use App\Helpers\Formulas;
use App\Models\Attendance;
use App\Models\Comment;
use App\Models\Enrollment;
use Barryvdh\Snappy\Facades\SnappyPdf;

class ReportController extends Controller
{
    public function generateQuarter($school_year_id, $student_id)
    {
        $record = Enrollment::with(['gradeLevel', 'schoolYear', 'student'])
            ->where('student_id', $student_id)
            ->where('school_year_id', $school_year_id)
            ->first();

        $type = 'semestral';
        $quarter = 2;

        $schoolYearLabel = $record->schoolYear->label;
        $schoolYear = (int) explode('-', $schoolYearLabel)[0];
        $gradeLevel = $record->gradeLevel;
        $student = $record->student;

        // --- Subjects: scoped to this enrollment's school_year_id + grade_level_id ---
        $subjects = $record->matchedSubjects()
            ->with('subjectCategory')
            ->with(['subjectUnit' => function ($query) use ($record) {
                $query->where('school_year_id', $record->school_year_id);
            }])
            ->get();

        $records = Formulas::buildGradesAndSubjects(
            $record->student_id,
            $record->grade_level_id,
            $record->school_year_id,
            $subjects,
            $type,
            $quarter,
            $schoolYear
        );

        // --- Comments: scoped to this enrollment's student + school_year ---
        $commentRecords = Comment::where('student_id', $record->student_id)
            ->where('school_year_id', $record->school_year_id)
            ->with('criteria')
            ->get();

        $comments = [];
        foreach ($commentRecords as $comment) {
            $comments[$comment->criteria_id] = [
                'criteria' => $comment->criteria,
                'score' => $comment->score,
            ];
        }

        // --- Attendance: scoped to this enrollment's student + school_year ---
        $attendanceRecord = Attendance::where('student_id', $record->student_id)
            ->where('school_year_id', $record->school_year_id)
            ->first();

        $attendance = [];
        if ($attendanceRecord && $attendanceRecord->attendance) {
            $decoded = json_decode($attendanceRecord->attendance, true);

            foreach ($decoded as $key => $att) {
                $attendance[$key] = [
                    'absent' => $att['A'] ?? 0,
                    'tardy' => $att['T'] ?? 0,
                ];
            }
        }

        $data = [
            'grades' => $records['grades'],
            'gradeLevel' => $gradeLevel,
            'subjects' => $records['subjects'] ?? [],
            'quarter' => $quarter,
            'type' => $type,
            'student' => $student,
            'comments' => $comments,
            'schoolYear' => $schoolYear,
            'attendance' => $attendance,
        ];

        dd($data);
        if (config('app.env') == 'local') {
            return view('reports.'.config('app.name').'.quarter', $data);
        }

        $viewName = 'reports.'.config('app.name').'.templates.upper_school';
        $pdf = SnappyPdf::loadView($viewName, $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait');

        return $pdf->inline("Report-{$student_id}.pdf");
    }

    public function generateTOR($student_id)
    {
        $type = 'semestral';
        $quarter = 2;

        $enrollments = Enrollment::where('student_id', $student_id)
            ->with(['gradeLevel', 'schoolYear'])
            ->orderBy('school_year_id')
            ->get();

        $student = $enrollments->first()?->student()->first();

        $torRecords = [];

        foreach ($enrollments as $enrollment) {
            $schoolYearLabel = $enrollment->schoolYear->label;
            $schoolYears[] = $schoolYearLabel;
            $schoolYear = (int) explode('-', $schoolYearLabel)[0];
            $gradeLevel = $enrollment->gradeLevel->label;
            $subjects = $enrollment->matchedSubjects()->with('subjectCategory')->get();

            $bucket = Formulas::buildGradesAndSubjects(
                $enrollment->student_id,
                $enrollment->grade_level_id,
                $enrollment->school_year_id,
                $subjects,
                $type,
                $quarter,
                $schoolYear
            );

            $torRecords[] = [
                'gradeLevel' => $gradeLevel,
                'schoolYear' => $schoolYear,
                'subjects' => $bucket['subjects'] ?? [],
                'grades' => $bucket['grades'] ?? [],
            ];
        }

        $data = [
            'records' => $torRecords,
            'student' => $student,
            'quarter' => $quarter,
            'type' => $type,
            'comments' => [],
            'attendance' => [],
            'schoolyears' => $schoolYears,
        ];

        // dd($data);
        if (config('app.env') == 'local') {
            return view("reports." . config('app.name') . ".tor", $data);
        }

        $viewName = 'reports.'.config('app.name').'.tor';
        $pdf = SnappyPdf::loadView($viewName, $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait');

        return $pdf->inline("Report-{$student_id}.pdf");
    }

    public function generateAward($school_year_id, $student_id) {}

    public function generateMidQuarter($school_year_id, $student_id) {}

    public function generateSubject($school_year_id, $student_id) {}
}
