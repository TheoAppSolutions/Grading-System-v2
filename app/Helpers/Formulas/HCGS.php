<?php

namespace App\Helpers\Formulas;

/**
 * Grade computation helpers: per-subject final averages, general average,
 * effort ratings, and deportment averages.
 *
 * Moved from App\Formula to App\Helpers\Formulas.
 */
class HCGS
{
    /** Passing threshold used for subject and general-average remarks. */
    private const PASSING_GRADE = 60;

    /** Minimum general average required to remain eligible for honors. */
    private const HONORS_THRESHOLD = 90;

    /** Minimum effort rating required to avoid effort disqualification. */
    private const MIN_EFFORT_RATING = 4;

    /** School years from this year onward use a 2-quarter (semestral) grading period. */
    private const SEMESTRAL_CUTOFF_YEAR = 2025;

    /**
     * Compute each subject's final grade/effort/GPA contribution, then roll
     * everything up into the general average.
     *
     * @param array $gradeset      Working grade data, keyed by subject name and quarter.
     * @param iterable $subjects   Subjects to process; each needs a ->subjectCode property.
     * @param int $startCounter    First quarter to include (inclusive).
     * @param int $endCounter      Last quarter to include (inclusive).
     * @param bool $isAwardReport  If true, process a subject even without a final grade yet.
     * @param int $schoolyear      School year, determines quarterly vs semestral grading.
     * @return array The updated gradeset.
     */
    public static function getFinalAveragePerSubject(
    array $gradeset,
    iterable $subjects,
    int $startCounter,
    int $endCounter,
    bool $isAwardReport,
    int $schoolyear
    ): array {
        $lastSubjectCode = '';
        $totalUnits = 0;
        $totalGpa = [];

        // Disqualification indicators.
        $gradeset['disqualifyForHonors'] = false;
        $gradeset['disqualifyForEffort'] = false;
        $gradeset['disqualifyForDeportment'] = false;
        $gradeset['isEligibleForNextGrade'] = false;

        $expectedPeriods = self::expectedPeriodCount($schoolyear);

        foreach ($subjects as $subject) {
            $subjectCode = data_get($subject, 'subject_code');
            $hasFinalGrade = isset($gradeset[$subjectCode][$endCounter]['final'])
                && $gradeset[$subjectCode][$endCounter]['final'] !== '';

            if (!$hasFinalGrade && !$isAwardReport) {
                continue;
            }

            $subAverage = [];

            for ($quarter = $startCounter; $quarter <= $endCounter; $quarter++) {
                $quarterData = $gradeset[$subjectCode][$quarter] ?? null;

                if ($quarterData === null) {
                    continue;
                }

                if (($quarterData['final'] ?? '') !== '') {
                    $subAverage['final'][$quarter] = $quarterData['rawfinal'];
                }

                if (($quarterData['raweffort'] ?? '') !== '') {
                    $subAverage['raweffort'][$quarter] = $quarterData['raweffort'];

                    // Any single quarter below the minimum effort rating disqualifies the student.
                    if ($subAverage['raweffort'][$quarter] < self::MIN_EFFORT_RATING) {
                        $gradeset['disqualifyForEffort'] = true;
                    }
                }
            }

            if (!isset($subAverage['final'], $subAverage['raweffort'])) {
                continue;
            }

            if (count($subAverage['final']) !== $expectedPeriods) {
                continue;
            }

            $rawFinal = number_format(array_sum($subAverage['final']) / count($subAverage['final']), 2);
            $gradeset[$subjectCode][5]['rawfinal'] = $rawFinal;
            $gradeset[$subjectCode][5]['final'] = $rawFinal;

            if (count($subAverage['raweffort']) === $expectedPeriods) {
                $effort = self::getFinalEffort(array_sum($subAverage['raweffort']), $schoolyear);
                $gradeset[$subjectCode][5]['effort'] = $effort;
                $gradeset['finaleffort'][$subjectCode] = $effort;

                if ($effort < self::MIN_EFFORT_RATING) {
                    $gradeset['disqualifyForEffort'] = true;
                }
            }

            $gradeset[$subjectCode]['remarks'] =
                $gradeset[$subjectCode][5]['final'] >= self::PASSING_GRADE ? 'PASSED' : 'FAILED';

            if (!isset($gradeset[$subjectCode]['unit']) || $lastSubjectCode === $subjectCode) {
                continue;
            }

            $lastSubjectCode = $subjectCode;
            $gradeset['finalave'][$subjectCode] = $gradeset[$subjectCode][5]['final'] * $gradeset[$subjectCode]['unit'];

            if ($gradeset[$subjectCode]['unit'] > 0) {
                $totalUnits += $gradeset[$subjectCode]['unit'];
                $gradeset[$subjectCode]['unit'] = number_format($gradeset[$subjectCode]['unit'], 2);
                $gradeset[$subjectCode]['grade_point'] = number_format(
                    letterGrade($gradeset[$subjectCode][5]['final'])['gpa'] * $gradeset[$subjectCode]['unit'],
                    2
                );
                $totalGpa[] = $gradeset[$subjectCode]['grade_point'];
            } else {
                $gradeset[$subjectCode]['unit'] = 0;
            }
        }

        if (isset($gradeset['finalave']) && $totalUnits > 1) {
            $gradeset = self::getGeneralAverage($gradeset, $totalUnits, $totalGpa);
        }

        return $gradeset;
    }

    /**
     * Compute the general average, GPA, and honors eligibility from
     * accumulated per-subject data.
     *
     * @param array $gradeset
     * @param float $totalUnits
     * @param array $totalGpa
     * @return array The updated gradeset.
     */
    public static function getGeneralAverage(array $gradeset, float $totalUnits, array $totalGpa): array
    {
        $gradeset['totalunits'] = number_format($totalUnits, 2);
        $gradeset['totalgpa'] = number_format(array_sum($totalGpa) / $totalUnits, 2);

        $generalAverage = array_sum((array) $gradeset['finalave']) / $totalUnits;
        $gradeset['generalaverage'] = number_format($generalAverage, 3);
        $gradeset['generalremarks'] = $generalAverage >= self::PASSING_GRADE ? 'PASSED' : 'FAILED';
        $gradeset['disqualifyForHonors'] = $generalAverage < self::HONORS_THRESHOLD;

        return $gradeset;
    }

    /**
     * Convert a summed raw effort score into a final effort rating,
     * averaged over the number of grading periods for the school year.
     *
     * @param float $effortGrades Sum of raw effort scores across periods.
     * @param int $schoolyear
     * @return float
     */
    public static function getFinalEffort(float $effortGrades, int $schoolyear): float
    {
        return $effortGrades / self::expectedPeriodCount($schoolyear);
    }

    /**
     * Average deportment scores per quarter and, once all periods are in,
     * compute the overall deportment average.
     *
     * @param array $gradeset
     * @param int $schoolyear
     * @param int $startCounter
     * @param int $endCounter
     * @return array The updated gradeset.
     */
    public static function getDeportmentAverage(
        array $gradeset,
        int $schoolyear,
        int $startCounter = 1,
        int $endCounter = 2
    ): array {
        for ($quarter = $startCounter; $quarter <= $endCounter; $quarter++) {
            $quarterScores = $gradeset['deportment'][$quarter] ?? null;

            if (empty($quarterScores)) {
                continue;
            }

            $average = array_sum($quarterScores) / count($quarterScores);
            $gradeset['rawdeportment'][$quarter] = number_format($average, 3);
            $gradeset['deportment'][$quarter] = round($average);
        }

        $expectedPeriods = self::expectedPeriodCount($schoolyear);

        if (!empty($gradeset['deportment'][$endCounter]) && count($gradeset['deportment']) === $expectedPeriods) {
            $gradeset['deportment'][5] = number_format(
                array_sum($gradeset['rawdeportment']) / count($gradeset['rawdeportment']),
                3
            );
        }

        return $gradeset;
    }

    /**
     * Number of grading periods expected for a given school year:
     * 2 (semestral) from SEMESTRAL_CUTOFF_YEAR onward, 4 (quarterly) before that.
     *
     * @param int $schoolyear
     * @return int
     */
    private static function expectedPeriodCount(int $schoolyear): int
    {
        return $schoolyear >= self::SEMESTRAL_CUTOFF_YEAR ? 2 : 4;
    }
}