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
            $record = [];
            $record['midquarter'] = number_format($grade['midquarter'], 2);
            $record['final'] = number_format($grade['final'], 2);
            $record['effort'] = $grade['effort'];
            $record['deportment'] = $grade['deportment'];
            $record['remarks'] = $grade['remarks'];

            // Formulas::getFinalAveragePerSubject reads 'rawfinal' (unformatted, for summing)
            // and 'raweffort' alongside the display-formatted 'final' set above.
            $record['rawfinal'] = $grade['final'];
            $record['raweffort'] = $grade['effort'];

            $records['grades'][$subjectCode][$grade->quarter] = $record;

            // Deportment average expects an array of raw scores per quarter to average over.
            $records['grades']['deportment'][$grade->quarter][] = $grade['deportment'];

            if ($grade->quarter == 4) {
                $type = "quarter";
            }
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

    /**
     * Convert a numeric final grade (0-100) into its letter grade.
     *
     * A+  97-100      C+  77-79.99
     * A   93-96.99    C   73-76.99
     * A-  90-92.99    C-  70-72.99
     * B+  87-89.99    D+  67-69.99
     * B   83-86.99    D   63-66.99
     * B-  80-82.99    D-  60-62.99
     *                 F   Below 60
     */
    public static function getFinalLetterGrade(int|float|string|null $grade): ?string
    {
        if ($grade === null || $grade === '') {
            return null;
        }

        $grade = (float) $grade;

        return match(true) {
            $grade >= 97 => 'A+',
            $grade >= 93 => 'A',
            $grade >= 90 => 'A-',
            $grade >= 87 => 'B+',
            $grade >= 83 => 'B',
            $grade >= 80 => 'B-',
            $grade >= 77 => 'C+',
            $grade >= 73 => 'C',
            $grade >= 70 => 'C-',
            $grade >= 67 => 'D+',
            $grade >= 63 => 'D',
            $grade >= 60 => 'D-',
            default      => 'F',
        };
    }
}
