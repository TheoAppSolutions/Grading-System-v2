<?php

namespace App\Http\Controllers;

use App\Helpers\Formulas;
use App\Models\Enrollment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportController extends Controller
{
    public function generateQuarter($school_year_id, $student_id)
    {
        $record = Enrollment::with(['gradeLevel', 'schoolYear', 'student'])
            ->where('student_id', $student_id)
            ->where('school_year_id', $school_year_id)
            ->firstOrFail();

        $type = 'semestral';
        $quarter = 2;

        $bucket = $this->buildEnrollmentBucket($record, $type, $quarter);

        $data = [
            'grades' => $bucket['grades'],
            'gradeLevel' => $record->gradeLevel,
            'subjects' => $bucket['subjects'],
            'quarter' => $quarter,
            'type' => $type,
            'student' => $record->student,
            'schoolYear' => $bucket['schoolYear'],
            'homeroom' => $bucket['homeroom'],
            'principal' => $bucket['principal'],
            'comments' => Formulas::getComments($record->student_id, $record->school_year_id, 'quarter'),
            'attendance' => Formulas::getAttendance($record->student_id, $record->school_year_id),
        ];

        return $this->renderOrGeneratePdf(
            viewName: 'reports.'.strtolower(config('app.name')).'.quarter',
            data: $data,
            filename: "Report-{$student_id}.pdf"
        );
    }

    public function generateTOR($student_id)
    {
        $type = 'semestral';
        $quarter = 2;

        $enrollments = Enrollment::where('student_id', $student_id)
            ->with(['gradeLevel', 'schoolYear'])
            ->orderBy('school_year_id')
            ->get();

        if ($enrollments->isEmpty()) {
            throw new NotFoundHttpException('No enrollment history was found for this student.');
        }

        $student = $enrollments->first()->student;

        $torRecords = [];
        $schoolYears = [];

        foreach ($enrollments as $enrollment) {
            $bucket = $this->buildEnrollmentBucket($enrollment, $type, $quarter);
            $schoolYears[] = $enrollment->schoolYear->label;

            $torRecords[] = [
                'gradeLevel' => $enrollment->gradeLevel->label,
                'schoolYear' => $bucket['schoolYear'],
                'subjects' => $bucket['subjects'],
                'grades' => $bucket['grades'],
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

        return $this->renderOrGeneratePdf(
            viewName: 'reports.'.strtolower(config('app.name')).'.tor',
            data: $data,
            filename: "Report-{$student_id}.pdf"
        );
    }

    /**
     * Build the grades/subjects bucket for a single enrollment record,
     * plus the parsed numeric school year.
     */
    private function buildEnrollmentBucket(Enrollment $enrollment, string $type, int $quarter): array
    {
        $schoolYear = $enrollment->schoolYear->label;
        $subjects = $enrollment->matchedSubjects()->get();

        $result = Formulas::buildGradesAndSubjects(
            $enrollment->student_id,
            $enrollment->grade_level_id,
            $enrollment->school_year_id,
            $subjects,
            $type,
            $quarter,
            $schoolYear
        );

        return [
            'schoolYear' => $schoolYear,
            'subjects' => $result['subjects'] ?? [],
            'grades' => $result['grades'] ?? [],
            'homeroom' => Formulas::getHomeroomAdvisor($enrollment->school_year_id, $enrollment->grade_level_id),
            'principal' => Formulas::getPrincipal(),
        ];
    }
    
    /**
     * Either dump the raw Blade view (local env) or render the PDF via Snappy.
     */
    private function renderOrGeneratePdf(string $viewName, array $data, string $filename)
    {
        abort_unless(View::exists($viewName), 404, "Report template [{$viewName}] was not found.");

        if (config('app.env') == 'local') {
            return view($viewName, $data);
        }

        $pdf = SnappyPdf::loadView($viewName, $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait');

        return $pdf->inline($filename);
    }

}
