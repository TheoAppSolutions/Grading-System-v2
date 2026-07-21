<?php

namespace App\Helpers;

// use App\Helpers\BIS\FormulaHelpers as Formula;
use App\Helpers\HCGS\FormulaHelpers as Formula;
use App\Models\Grade;
use App\Models\Subject;

class Formulas extends Formula
{
    public static function buildGradesAndSubjects(
        int $studentId,
        int $gradeLevelId,
        int $schoolYearId,
        $matchedSubjects,
        string $type,
        ?int $quarter,
        int $schoolYear
    ): array {
        $records = [];

        foreach ($matchedSubjects as $subject) {
            $records['subjects'][$subject->subject_title] = $subject;
        }

        $grades = Grade::where('student_id', $studentId)
            ->where('grade_level_id', $gradeLevelId)
            ->where('school_year_id', $schoolYearId)
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
        foreach ($matchedSubjects as $subject) {
            if (isset($records['grades'][$subject->subject_code])) {
                $records['grades'][$subject->subject_code]['unit'] = $subject['subjectUnit'][0]['units'] ?? 0;
            }
        }

        // Formulas expects each subject object to expose ->subjectname; subject_code is
        // what we keyed the gradeset by above, so alias it here rather than changing the model.
        $subjectsForFormula = $matchedSubjects->map(function ($subject) {
            $subject->subjectname = $subject->subject_code;

            return $subject;
        });

        $startCounter = 1;
        $endCounter = $type === 'semestral' ? 2 : ($quarter ?: 4);

        $records['grades'] = self::getFinalAveragePerSubject(
            $records['grades'] ?? [],
            $subjectsForFormula,
            $startCounter,
            (int) $endCounter,
            false,
            $schoolYear
        );

        $records['grades'] = self::getDeportmentAverage(
            $records['grades'],
            $schoolYear,
            $startCounter,
            (int) $endCounter
        );

        return $records;
    }
}
