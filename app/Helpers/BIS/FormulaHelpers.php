<?php

namespace App\Helpers\BIS;

use App\Models\SubjectUnit;

class FormulaHelpers
{
    // ─── Constants ────────────────────────────────────────────────────────────
    private const FINAL_INDEX         = 5;
    private const PASSING_GRADE       = 75;
    private const HONORS_MIN_AVERAGE  = 92;

    private const LEGACY_SCHOOL_YEARS = ['2015-2016', '2016-2017'];
    private const COVID_SCHOOL_YEAR   = '2019-2020';

    private const GRADE_JHS_MAX       = 10;
    private const GRADE_SHS_MIN       = 11;

    private const EFFORT_MAJOR_PASS_THRESHOLD    = 4;   // must be < 4 to pass (JHS major)
    private const EFFORT_MINOR_PASS_THRESHOLD    = 3;   // must be <= 3 to pass
    private const EFFORT_MAPEH_SUM_PASS          = 8;   // combined MUSIC+ART+PE must be >= 8

    private const COMPOSITE_MAPEH = ['MUSIC', 'ART', 'PE'];
    private const COMPOSITE_TLE   = ['LIVELIHOOD', 'COMPUTER'];

    // ─── Public API ───────────────────────────────────────────────────────────

    public static function getFinalAveragePerSubject(
        array $gradeset,
        array $subjects,
        int   $startCounter,
        int   $endCounter,
        bool  $isAwardReport
    ): array {
        $units = SubjectUnit::getUnits();

        $gradeset = self::initializeFlags($gradeset);

        $lastSubjectName = '';
        $totalUnits      = 0;

        foreach ($subjects as $subject) {
            $subjectName = $subject->subjectname;

            if (!self::subjectHasFinalGrade($gradeset, $subjectName, $endCounter, $isAwardReport)) {
                continue;
            }

            $subAverage = self::collectQuarterlyAverages($gradeset, $subjectName, $startCounter, $endCounter);

            if (!self::hasRequiredAverages($subAverage)) {
                continue;
            }

            $gradeset = self::applySubjectFinal($gradeset, $subjectName, $subAverage);
            $gradeset = self::evaluateHonorsEligibility($gradeset, $subjectName, $subAverage);

            if (self::shouldApplyUnits($units, $subjectName, $lastSubjectName)) {
                $lastSubjectName = $subjectName;
                [$gradeset, $totalUnits] = self::applyUnitWeighting(
                    $gradeset,
                    $subject,
                    $subjectName,
                    $units,
                    $totalUnits
                );
                $gradeset = self::evaluateEffortEligibility($gradeset, $subject, $subjectName);
            }
        }

        $gradeset = self::applyCompositeSubjects($gradeset);

        if (isset($gradeset['finalave']) && $totalUnits >= 10) {
            $gradeset = self::getGeneralAverage($gradeset, $totalUnits);
        }

        return $gradeset;
    }

    public static function getMAPEH_TLE(array $gradeset): array
    {
        $units = Units::getUnits();

        for ($quarter = 1; $quarter <= self::FINAL_INDEX; $quarter++) {
            $gradeset = self::computeCompositeGrade(
                $gradeset,
                $quarter,
                'MAPEH',
                self::COMPOSITE_MAPEH,
                $units
            );

            $gradeset = self::computeCompositeGrade(
                $gradeset,
                $quarter,
                'TLE',
                self::COMPOSITE_TLE,
                $units
            );
        }

        foreach (['MAPEH', 'TLE'] as $composite) {
            if (isset($gradeset[$composite][self::FINAL_INDEX])) {
                $gradeset[$composite]['remarks'] = $gradeset[$composite][self::FINAL_INDEX]['final'] >= self::PASSING_GRADE
                    ? 'PASSED'
                    : 'FAILED';
            }
        }

        return $gradeset;
    }

    public static function getGeneralAverage(array $gradeset, float $totalUnits): array
    {
        $average = array_sum($gradeset['finalave']) / $totalUnits;

        $gradeset['generalaverage']      = number_format($average, 3);
        $gradeset['generalremarks']      = $average >= self::PASSING_GRADE ? 'PASSED' : 'FAILED';
        $gradeset['disqualifyForHonors'] = $average < self::HONORS_MIN_AVERAGE;
        $gradeset['eligible']            = self::resolveNextGradeEligibility($gradeset, $average);

        return $gradeset;
    }

    public static function getFinalEffort(int $effortSum, int $gradeLevel): float
    {
        return $gradeLevel <= self::GRADE_JHS_MAX
            ? self::resolveJhsEffort($effortSum)
            : self::resolveShsEffort($effortSum);
    }

    public static function getDeportmentAverage(array $gradeset, int $startCounter, int $endCounter): array
    {
        for ($quarter = $startCounter; $quarter <= $endCounter; $quarter++) {
            if (!self::quarterHasDeportment($gradeset, $quarter)) {
                continue;
            }

            $gradeset = self::computeQuarterDeportment($gradeset, $quarter);
        }

        $gradeset = self::computeFinalDeportment($gradeset, $endCounter);

        return $gradeset;
    }

    // ─── Initialization ───────────────────────────────────────────────────────

    private static function initializeFlags(array $gradeset): array
    {
        return array_merge($gradeset, [
            'disqualifyForHonors'     => false,
            'disqualifyForEffort'     => false,
            'disqualifyForDeportment' => false,
            'isEligibleForNextGrade'  => true,
        ]);
    }

    // ─── Per-Subject Helpers ──────────────────────────────────────────────────

    private static function subjectHasFinalGrade(
        array  $gradeset,
        string $subjectName,
        int    $endCounter,
        bool   $isAwardReport
    ): bool {
        return $isAwardReport
            || (
                isset($gradeset[$subjectName][$endCounter]['final'])
                && $gradeset[$subjectName][$endCounter]['final'] !== ''
            );
    }

    private static function collectQuarterlyAverages(
        array  $gradeset,
        string $subjectName,
        int    $startCounter,
        int    $endCounter
    ): array {
        $subAverage = [];

        for ($quarter = $startCounter; $quarter <= $endCounter; $quarter++) {
            $gradeSlot = $gradeset[$subjectName][$quarter] ?? [];

            if (!empty($gradeSlot['final'])) {
                $subAverage['final'][$quarter] = $gradeSlot['rawfinal'];
            }

            if (isset($gradeSlot['raweffort']) && $gradeSlot['raweffort'] !== '') {
                $subAverage['raweffort'][$quarter] = $gradeSlot['raweffort'];
            }
        }

        return $subAverage;
    }

    private static function hasRequiredAverages(array $subAverage): bool
    {
        return isset($subAverage['final'], $subAverage['raweffort']);
    }

    private static function applySubjectFinal(array $gradeset, string $subjectName, array $subAverage): array
    {
        $rawFinal = array_sum($subAverage['final']) / count($subAverage['final']);
        $gradeset[$subjectName][self::FINAL_INDEX]['rawfinal'] = $rawFinal;
        $gradeset[$subjectName][self::FINAL_INDEX]['final']    = number_format($rawFinal, 3);

        $gradeset[$subjectName][self::FINAL_INDEX]['effort'] = self::isCovidYear()
            ? number_format(array_sum($subAverage['raweffort']) / count($subAverage['raweffort']), 0)
            : self::getFinalEffort(array_sum($subAverage['raweffort']), $gradeset['gradelevel']);

        $gradeset[$subjectName]['remarks'] = $rawFinal >= self::PASSING_GRADE ? 'PASSED' : 'FAILED';

        return $gradeset;
    }

    private static function evaluateHonorsEligibility(array $gradeset, string $subjectName, array $subAverage): array
    {
        if ($gradeset['disqualifyForHonors']) {
            return $gradeset;
        }

        $quarterCount = count($subAverage['final']);
        $gradeLevel   = $gradeset['gradelevel'];
        $isChinese    = $subjectName === 'CHINESE';

        $isJhs = $gradeLevel <= self::GRADE_JHS_MAX;
        $isShs = $gradeLevel >= self::GRADE_SHS_MIN;

        // JHS: needs all 4 quarters (CHINESE exempt from this rule)
        if ($isJhs && !$isChinese && $quarterCount !== 4) {
            $gradeset['disqualifyForHonors'] = true;
        }

        // SHS: needs all 2 semesters
        if ($isShs && $quarterCount !== 2) {
            $gradeset['disqualifyForHonors'] = true;
        }

        return $gradeset;
    }

    private static function shouldApplyUnits(array $units, string $subjectName, string $lastSubjectName): bool
    {
        return isset($units[$subjectName]) && $lastSubjectName !== $subjectName;
    }

    private static function applyUnitWeighting(
        array  $gradeset,
        object $subject,
        string $subjectName,
        array  $units,
        float  $totalUnits
    ): array {
        $unit = $units[$subjectName]['unit'];

        $gradeset[$subjectName]['unit']      = number_format($unit, 2);
        $gradeset['finalave'][$subjectName]  = $gradeset[$subjectName][self::FINAL_INDEX]['final'] * $unit;

        $totalUnits += $unit;

        return [$gradeset, $totalUnits];
    }

    private static function evaluateEffortEligibility(array $gradeset, object $subject, string $subjectName): array
    {
        if ($gradeset['disqualifyForEffort']) {
            return $gradeset;
        }

        $effort = $gradeset[$subjectName][self::FINAL_INDEX]['effort'];

        if ($subject->deportable == 1) {
            // Major subject
            $gradeset['finaleffort'][$subjectName] = $effort;
            $gradeset['disqualifyForEffort']        = $effort >= self::EFFORT_MAJOR_PASS_THRESHOLD;
            return $gradeset;
        }

        if ($subjectName === 'CHINESE') {
            return $gradeset;
        }

        // Minor subjects
        if (in_array($subjectName, self::COMPOSITE_TLE)) {
            $gradeset['disqualifyForEffort'] = $effort > self::EFFORT_MINOR_PASS_THRESHOLD;
        }

        // MAPEH combined effort check
        if (self::allMapehEffortsPresent($gradeset)) {
            $sum = array_sum(array_map(
                fn($s) => $gradeset[$s][self::FINAL_INDEX]['effort'],
                self::COMPOSITE_MAPEH
            ));
            $gradeset['disqualifyForEffort'] = $sum < self::EFFORT_MAPEH_SUM_PASS;
        }

        return $gradeset;
    }

    private static function allMapehEffortsPresent(array $gradeset): bool
    {
        foreach (self::COMPOSITE_MAPEH as $subject) {
            if (!isset($gradeset[$subject][self::FINAL_INDEX]['effort'])) {
                return false;
            }
        }
        return true;
    }

    private static function applyCompositeSubjects(array $gradeset): array
    {
        $schoolYear  = MyTools::getSchoolYear();
        $isLegacy    = in_array($schoolYear, self::LEGACY_SCHOOL_YEARS);
        $gradeLevel  = $gradeset['gradelevel'];

        if (!$isLegacy && $gradeLevel <= self::GRADE_JHS_MAX) {
            unset($gradeset['finalave']['CHINESE']);
            $gradeset = self::getMAPEH_TLE($gradeset);
        }

        return $gradeset;
    }

    // ─── Composite Subjects ───────────────────────────────────────────────────

    private static function computeCompositeGrade(
        array  $gradeset,
        int    $quarter,
        string $compositeKey,
        array  $components,
        array  $units
    ): array {
        foreach ($components as $component) {
            if (empty($gradeset[$component][$quarter]['final'])) {
                return $gradeset; // Not all components present
            }
        }

        $weightedSum = 0;
        $totalUnit   = 0;

        foreach ($components as $component) {
            $subjectId    = $gradeset[$component]['subject_id'];
            $unit         = $units[$subjectId]['unit'];
            $weightedSum += $gradeset[$component][$quarter]['rawfinal'] * $unit;
            $totalUnit   += $unit;
        }

        $rawFinal = number_format($weightedSum / $totalUnit, 3);
        $gradeset[$compositeKey][$quarter]['rawfinal'] = $rawFinal;
        $gradeset[$compositeKey][$quarter]['final']    = $quarter === self::FINAL_INDEX
            ? $rawFinal
            : (round($rawFinal) <= 0 ? '' : round($rawFinal));

        return $gradeset;
    }

    // ─── General Average Helpers ──────────────────────────────────────────────

    private static function resolveNextGradeEligibility(array $gradeset, float $average): string
    {
        if ($average < self::PASSING_GRADE) {
            return '';
        }

        return $gradeset['gradelevel'] <= self::GRADE_SHS_MIN
            ? MyTools::ordinal(getNextGradeLevel($gradeset['gradelevel'])) . ' Grade'
            : '';
    }

    // ─── Effort Helpers ───────────────────────────────────────────────────────

    private static function resolveJhsEffort(int $effortSum): float
    {
        if (self::isLegacyYear()) {
            return match (true) {
                $effortSum >= 11 => 3,
                $effortSum >= 7  => 2,
                $effortSum >= 1  => 1,
                default          => $effortSum,
            };
        }

        return match (true) {
            $effortSum >= 19 => 5,
            $effortSum >= 15 => 4,
            $effortSum >= 11 => 3,
            $effortSum >= 7  => 2,
            $effortSum >= 1  => 1,
            default          => $effortSum,
        };
    }

    private static function resolveShsEffort(int $effortSum): float
    {
        if (self::isLegacyYear()) {
            return match ($effortSum) {
                6       => 3,
                5, 4    => 2,
                default => 1,
            };
        }

        // Note: case 2 intentionally maps to 2.0 (not 1.5) — preserved from original business rule.
        // Verify with school admin whether this is correct or a historical bug.
        return match ($effortSum) {
            10      => 5,
            9       => 4.5,
            8       => 4,
            7       => 3.5,
            6       => 3,
            5       => 2.5,
            4       => 2,
            3       => 1.5,
            2       => 2.0,  // ⚠ intentionally out of sequence — confirm with admin
            default => 1,
        };
    }

    // ─── Deportment Helpers ───────────────────────────────────────────────────

    private static function quarterHasDeportment(array $gradeset, int $quarter): bool
    {
        return isset($gradeset['deportment'][$quarter]) && $gradeset['deportment'][$quarter] > 0;
    }

    private static function computeQuarterDeportment(array $gradeset, int $quarter): array
    {
        $scores = $gradeset['deportment'][$quarter];
        $avg    = array_sum($scores) / count($scores);

        $external = getDeportmentGrades($gradeset['student_id']);
        if (isset($external[$quarter])) {
            $avg = ($avg + $external[$quarter]) / 2;
        }

        $gradeset['rawdeportment'][$quarter]  = number_format($avg, 3);
        $gradeset['deportment'][$quarter]     = round($avg);

        return $gradeset;
    }

    private static function computeFinalDeportment(array $gradeset, int $endCounter): array
    {
        $raw = $gradeset['rawdeportment'] ?? [];

        if (empty($raw)) {
            return $gradeset;
        }

        $shouldCompute = self::isCovidYear()
            || (isset($gradeset['deportment'][$endCounter]) && $gradeset['deportment'][$endCounter] > 0);

        if ($shouldCompute) {
            $gradeset['deportment'][self::FINAL_INDEX] = number_format(
                array_sum($raw) / count($raw),
                3
            );
        }

        return $gradeset;
    }

    // ─── School Year Utilities ────────────────────────────────────────────────

    private static function isLegacyYear(): bool
    {
        return in_array(MyTools::getSchoolYear(), self::LEGACY_SCHOOL_YEARS);
    }

    private static function isCovidYear(): bool
    {
        return MyTools::getSchoolYear() === self::COVID_SCHOOL_YEAR;
    }
}
