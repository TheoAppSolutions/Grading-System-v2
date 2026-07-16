<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\SubjectUser;
use App\Helpers\Formulas;

class ReportController extends Controller
{
    public function generatePdf($id)
    {
        $record = Enrollment::find($id);
        $subjects = $record->matchedSubjects()->with('subjectCategory')->get();
        $students = $record->student()->first();
        $gradeLevel = $record->gradeLevel->label;
        $quarter = request()->query('quarter');
        $type = request()->query('type', 'quarter');
        $schoolYearLabel = $record->schoolYear()->first()->label;

        // Formulas needs the school year as an int (e.g. "2025-2026" -> 2025)
        // to decide quarterly (4 periods) vs semestral (2 periods) grading.
        $schoolYear = (int) explode('-', $schoolYearLabel)[0];

        $records = [];
        foreach ($subjects as $subject) {
            $records['subjects'][$subject->subject_title] = $subject;
        }

        $grades = Grade::where('student_id', $record->student_id)
            ->where('grade_level_id', $record->grade_level_id)
            ->where('school_year_id', $record->school_year_id)
            ->with('subject')
            ->get();

        foreach ($grades as $grade) {
            $subjectCode = $grade->subject->subject_code;

            $records['grades'][$subjectCode][$grade->quarter]['midquarter'] = number_format($grade['midquarter'], 2);
            $records['grades'][$subjectCode][$grade->quarter]['final'] = number_format($grade['final'], 2);
            $records['grades'][$subjectCode][$grade->quarter]['effort'] = $grade['effort'];
            $records['grades'][$subjectCode][$grade->quarter]['deportment'] = $grade['deportment'];
            $records['grades'][$subjectCode][$grade->quarter]['remarks'] = $grade['remarks'];

            // Formulas::getFinalAveragePerSubject reads 'rawfinal' (unformatted, for summing)
            // and 'raweffort' alongside the display-formatted 'final' set above.
            $records['grades'][$subjectCode][$grade->quarter]['rawfinal'] = $grade['final'];
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

        $startCounter = $type === 'semestral' ? 1 : 1;
        $endCounter = $type === 'semestral' ? 2 : ($quarter ?: 4);

        $records['grades'] = Formulas::getFinalAveragePerSubject(
            $records['grades'],
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

        $data = [
            'grades' => $records['grades'],
            'gradeLevel' => $gradeLevel,
            'subjects' => $records['subjects'],
            'quarter' => $quarter,
            'type' => $type,
            'student' => $students,
            'comments' => [],
            'schoolYear' => $schoolYear,
        ];

        return view("reports." . config('app.name') . ".templates.upper_school", $data);
        $viewName = "reports." . config('app.name') . ".templates.upper_school";
        $pdf = SnappyPdf::loadView($viewName, $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait');

        return $pdf->inline("Report-{$id}.pdf");
    }
}
