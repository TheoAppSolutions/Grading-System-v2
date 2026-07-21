<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Comment;
use App\Models\CommentCriteria;
use App\Models\Enrollment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Helpers\Formulas;

class ReportController extends Controller
{
    public function generateQuarter($id)
    {
        // Load enrollment with all needed relationships up front
        $record = Enrollment::with(['gradeLevel', 'schoolYear', 'student'])->findOrFail($id);

        $type    = 'semestral';
        $quarter = 2;

        $schoolYearLabel = $record->schoolYear->label;
        $schoolYear      = (int) explode('-', $schoolYearLabel)[0];
        $gradeLevel      = $record->gradeLevel->label;
        $student         = $record->student;

        // --- Subjects: scoped to this enrollment's school_year_id + grade_level_id ---
        $subjects = $record->matchedSubjects()->with('subjectCategory')->get();

        $records = [];
        foreach ($subjects as $subject) {
            $records['subjects'][$subject->subject_title] = $subject;
        }

        // --- Grades: scoped to student + grade_level + school_year ---
        $grades = Grade::where('student_id', $record->student_id)
            ->where('grade_level_id', $record->grade_level_id)
            ->where('school_year_id', $record->school_year_id)
            ->with('subject')
            ->get();

        foreach ($grades as $grade) {
            $subjectCode = $grade->subject->subject_code;

            $records['grades'][$subjectCode][$grade->quarter]['midquarter'] = number_format($grade['midquarter'], 2);
            $records['grades'][$subjectCode][$grade->quarter]['final']      = number_format($grade['final'], 2);
            $records['grades'][$subjectCode][$grade->quarter]['effort']     = $grade['effort'];
            $records['grades'][$subjectCode][$grade->quarter]['deportment'] = $grade['deportment'];
            $records['grades'][$subjectCode][$grade->quarter]['remarks']    = $grade['remarks'];

            // Formulas::getFinalAveragePerSubject reads 'rawfinal' (unformatted, for summing)
            // and 'raweffort' alongside the display-formatted 'final' set above.
            $records['grades'][$subjectCode][$grade->quarter]['rawfinal']  = $grade['final'];
            $records['grades'][$subjectCode][$grade->quarter]['raweffort'] = $grade['effort'];

            // Deportment average expects an array of raw scores per quarter to average over.
            $records['grades']['deportment'][$grade->quarter][] = $grade['deportment'];
        }

        // Attach units onto each subject bucket so getFinalAveragePerSubject can compute
        // finalave/GPA. Adjust `units` to whatever property actually holds the unit count.
        foreach ($subjects as $subject) {
            if (isset($records['grades'][$subject->subject_code])) {
                $records['grades'][$subject->subject_code]['unit'] = $subject->units ?? 0;
            }
        }

        // Formulas expects each subject object to expose ->subjectname; subject_code is
        // what we keyed the gradeset by above, so alias it here rather than changing the model.
        $subjectsForFormula = $subjects->map(function ($subject) {
            $subject->subjectname = $subject->subject_code;
            return $subject;
        });

        $startCounter = 1;
        $endCounter   = $type === 'semestral' ? 2 : ($quarter ?: 4);

        $records['grades'] = Formulas::getFinalAveragePerSubject(
            $records['grades'] ?? [],
            $subjectsForFormula,
            $startCounter,
            (int) $endCounter,
            false,
            $schoolYear
        );

        $records['grades'] = Formulas::getDeportmentAverage(
            $records['grades'],
            $schoolYear,
            $startCounter,
            (int) $endCounter
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
                'score'    => $comment->score,
            ];
        }

        // --- Attendance: scoped to this enrollment's student + school_year ---
        $attendanceRecords = Attendance::where('student_id', $record->student_id)
            ->where('school_year_id', $record->school_year_id)
            ->orderBy('month')
            ->get();

        $attendance = [];
        foreach ($attendanceRecords as $att) {
            $attendance[$att->month_name] = [
                'present'   => $att->days_present,
                'tardiness' => $att->days_tardiness,
            ];
        }

        $data = [
            'grades'      => $records['grades'],
            'gradeLevel'  => $gradeLevel,
            'subjects'    => $records['subjects'] ?? [],
            'quarter'     => $quarter,
            'type'        => $type,
            'student'     => $student,
            'comments'    => $comments,
            'schoolYear'  => $schoolYear,
            'attendance'  => $attendance,
        ];

        // dd($data);
        return view("reports." . config('app.name') . ".quarter", $data);
        $viewName = "reports." . config('app.name') . ".templates.upper_school";
        $pdf = SnappyPdf::loadView($viewName, $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait');

        return $pdf->inline("Report-{$id}.pdf");
    }

    public function generateTOR($student_id)
    {
        $type = 'semestral';
        $quarter = 2;

        $schoolYears = SchoolYear::all()->pluck('label')->toArray();

        // Fetch all enrollment records for this student, eager-load relationships
        $enrollments = Enrollment::where('student_id', $student_id)
            ->with(['gradeLevel', 'schoolYear'])
            ->orderBy('school_year_id')
            ->get();

        // Resolve the student from the first enrollment
        $student = $enrollments->first()?->student()->first();

        $torRecords = [];

        foreach ($enrollments as $enrollment) {
            $schoolYearLabel = $enrollment->schoolYear->label;
            $schoolYear      = (int) explode('-', $schoolYearLabel)[0];
            $gradeLevel      = $enrollment->gradeLevel->label;

            // Subjects matched to this enrollment's school year + grade level
            $subjects = $enrollment->matchedSubjects()->with('subjectCategory')->get();

            $bucket = [];

            foreach ($subjects as $subject) {
                $bucket['subjects'][$subject->subject_title] = $subject;
            }

            // Grades for this enrollment
            $grades = Grade::where('student_id', $enrollment->student_id)
                ->where('grade_level_id', $enrollment->grade_level_id)
                ->where('school_year_id', $enrollment->school_year_id)
                ->with('subject')
                ->get();

            foreach ($grades as $grade) {
                $subjectCode = $grade->subject->subject_code;

                $bucket['grades'][$subjectCode][$grade->quarter]['midquarter'] = number_format($grade['midquarter'], 2);
                $bucket['grades'][$subjectCode][$grade->quarter]['final']      = number_format($grade['final'], 2);
                $bucket['grades'][$subjectCode][$grade->quarter]['effort']     = $grade['effort'];
                $bucket['grades'][$subjectCode][$grade->quarter]['deportment'] = $grade['deportment'];
                $bucket['grades'][$subjectCode][$grade->quarter]['remarks']    = $grade['remarks'];

                // Formulas::getFinalAveragePerSubject reads 'rawfinal' (unformatted, for summing)
                // and 'raweffort' alongside the display-formatted 'final' set above.
                $bucket['grades'][$subjectCode][$grade->quarter]['rawfinal'] = $grade['final'];
                $bucket['grades'][$subjectCode][$grade->quarter]['raweffort'] = $grade['effort'];

                // Deportment average expects an array of raw scores per quarter to average over.
                $bucket['grades']['deportment'][$grade->quarter][] = $grade['deportment'];
            }

            // Attach units onto each subject bucket so getFinalAveragePerSubject can compute
            // finalave/GPA. Adjust `units` to whatever property actually holds the unit count.
            foreach ($subjects as $subject) {
                if (isset($bucket['grades'][$subject->subject_code])) {
                    $bucket['grades'][$subject->subject_code]['unit'] = $subject->units ?? 0;
                }
            }

            // Formulas expects each subject object to expose ->subjectname; subject_code is
            // what we keyed the gradeset by above, so alias it here rather than changing the model.
            $subjectsForFormula = $subjects->map(function ($subject) {
                $subject->subjectname = $subject->subject_code;
                return $subject;
            });

            $startCounter = $type === 'semestral' ? 1 : 1;
            $endCounter   = $type === 'semestral' ? 2 : ($quarter ?: 4);

            $bucket['grades'] = Formulas::getFinalAveragePerSubject(
                $bucket['grades'] ?? [],
                $subjectsForFormula,
                $startCounter,
                (int) $endCounter,
                false,
                $schoolYear
            );

            $bucket['grades'] = Formulas::getDeportmentAverage(
                $bucket['grades'],
                $schoolYear,
                $startCounter,
                (int) $endCounter
            );

            $torRecords[] = [
                'gradeLevel' => $gradeLevel,
                'schoolYear' => $schoolYear,
                'subjects'   => $bucket['subjects'] ?? [],
                'grades'     => $bucket['grades'] ?? [],
            ];
        }

        $data = [
            'records'    => $torRecords,
            'student'    => $student,
            'quarter'    => $quarter,
            'type'       => $type,
            'comments'   => [],
            'attendance' => [],
            'schoolyears' => $schoolYears,
        ];

        // dd($data);
        return view("reports." . config('app.name') . ".tor", $data);
        $viewName = "reports." . config('app.name') . ".templates.upper_school";
        $pdf = SnappyPdf::loadView($viewName, $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait');

        return $pdf->inline("Report-{$student_id}.pdf");
    }

    public function generateAward($id) {}

    public function generateMidQuarter($id) {}

    public function generateSubject($id) {}
    
}
