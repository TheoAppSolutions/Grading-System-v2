<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use App\Models\Grade;

class ReportController extends Controller
{
    public function generatePdf($id)
    {
        $record = Enrollment::with('subjects')->find($id);
        $subjects = $record->matchedSubjects()->with('subjectCategory')->get();
        $students = $record->student()->first();
        $records = [];
        foreach ($subjects as $subject) {
            $records['subjects'][$subject->subject_title] = $subject->toArray();
        }
        $records['student'] = $students->toArray();

        return view('reports.hcgs.upper_school.secondary_T2-T4', [
            'grades' => [],
            'subjects' => $records['subjects'],
            'quarter' => 4,
            'student' => $record['student'],
            'comments' => [],
        ]);

        $pdf = SnappyPdf::loadView('reports.hcgs.upper_school.secondary_T2-T4', [
            'grades' => [],
            'subjects' => $records['subjects'],
            'quarter' => 4,
            'student' => $record['student'],
            'comments' => [],
        ])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Landscape')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);

        return $pdf->inline("Report-{$id}.pdf");
    }
}
