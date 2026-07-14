<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\SubjectUser;

class ReportController extends Controller
{
    public function generatePdf($id)
    {
        $record = Enrollment::find($id);
        $subjects = $record->matchedSubjects()->with('subjectCategory')->get();
        $students = $record->student()->first();
        $gradeLevel = $record->gradeLevel->label;
        $quarter = request()->query('quarter', 4);
        $type = request()->query('type', 'quarter');
        $schoolYear = $record->schoolYear()->first()->label;

        $records = [];
        foreach ($subjects as $subject) {
            $records['subjects'][$subject->subject_title] = $subject->toArray();
        }

        $grades = Grade::where('student_id', $record->student_id)
            ->where('grade_level_id', $record->grade_level_id)
            ->where('school_year_id', $record->school_year_id)
            ->with('subject')
            ->get();

        foreach ($grades as $grade) {
            $records['grades'][$grade->subject->subject_title] = $grade->toArray();
        }

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

        // return view('reports.bis.upper_school.secondary_T2-T4', $data);
        $viewName = "reports." . config('app.name') . ".upper_school.secondary_T2-T4";
        $pdf = SnappyPdf::loadView($viewName, $data)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Portrait');
            // ->setOption('margin-top', 5)
            // ->setOption('margin-bottom', 5)
            // ->setOption('margin-left', 5)
            // ->setOption('margin-right', 5);

        return $pdf->inline("Report-{$id}.pdf");
    }
}
