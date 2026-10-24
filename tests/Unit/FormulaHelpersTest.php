<?php

namespace Tests\Unit;

use App\Helpers\Formulas;
use App\Helpers\HCGS\FormulaHelpers;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FormulaHelpersTest extends TestCase
{
    #[DataProvider('letterGradeProvider')]
    public function test_final_letter_grade_uses_expected_boundaries(
        int|float|string|null $score,
        ?string $expected
    ): void {
        $this->assertSame($expected, Formulas::getFinalLetterGrade($score));
    }

    public static function letterGradeProvider(): array
    {
        return [
            'empty value' => [null, null],
            'empty string' => ['', null],
            'A plus lower bound' => [97, 'A+'],
            'A lower bound' => [93, 'A'],
            'B plus lower bound' => [87, 'B+'],
            'C lower bound' => [73, 'C'],
            'D minus lower bound' => [60, 'D-'],
            'failing score' => [59.99, 'F'],
        ];
    }

    public function test_effort_is_averaged_over_four_periods_before_semestral_cutoff(): void
    {
        $this->assertSame(3.5, FormulaHelpers::getFinalEffort(14, 2024));
    }

    public function test_effort_is_averaged_over_two_periods_from_semestral_cutoff(): void
    {
        $this->assertSame(3.5, FormulaHelpers::getFinalEffort(7, 2025));
    }

    public function test_average_returns_empty_when_either_score_is_missing_or_zero(): void
    {
        $this->assertSame('', FormulaHelpers::average(80, 0));
        $this->assertSame('', FormulaHelpers::average(null, 80));
        $this->assertSame('85.00', FormulaHelpers::average(80, 90));
    }

    public function test_deportment_average_rounds_each_period_and_computes_final_average(): void
    {
        $gradeset = FormulaHelpers::getDeportmentAverage(
            ['deportment' => [1 => [4, 5], 2 => [3, 4]]],
            2025,
            1,
            2
        );

        $this->assertSame(5.0, $gradeset['deportment'][1]);
        $this->assertSame(4.0, $gradeset['deportment'][2]);
        $this->assertSame('4.000', $gradeset['deportment'][5]);
        $this->assertSame('4.500', $gradeset['rawdeportment'][1]);
    }

    public function test_general_average_sets_pass_remarks_and_honors_eligibility(): void
    {
        $result = FormulaHelpers::getGeneralAverage(
            ['finalave' => ['MATH' => 92, 'ENGLISH' => 94]],
            2,
            [92, 94]
        );

        $this->assertSame('93.000', $result['generalaverage']);
        $this->assertSame('PASSED', $result['generalremarks']);
        $this->assertFalse($result['disqualifyForHonors']);
        $this->assertSame('93.00', $result['totalgpa']);
        $this->assertSame('93.000', $result['term-summary__average']);
    }

    public function test_semestral_periods_are_not_averaged_into_the_first_display_slot(): void
    {
        $subjects = [(object) ['subject_code' => 'MATH']];
        $gradeset = [
            'MATH' => [
                1 => ['final' => '80.00', 'rawfinal' => 80, 'raweffort' => 4],
                2 => ['final' => '90.00', 'rawfinal' => 90, 'raweffort' => 4],
            ],
        ];

        $result = FormulaHelpers::getFinalAveragePerSubject(
            $gradeset,
            $subjects,
            1,
            2,
            false,
            2025
        );

        $this->assertSame('80.00', $result['MATH'][1]['final']);
        $this->assertSame('90.00', $result['MATH'][2]['final']);
        $this->assertSame('85.00', $result['MATH'][5]['final']);
    }

    public function test_final_grade_is_computed_when_effort_is_missing(): void
    {
        $subjects = [(object) ['subject_code' => 'MATH']];
        $gradeset = [
            'MATH' => [
                1 => ['final' => '80.00', 'rawfinal' => 80],
                2 => ['final' => '90.00', 'rawfinal' => 90],
            ],
        ];

        $result = FormulaHelpers::getFinalAveragePerSubject(
            $gradeset,
            $subjects,
            1,
            2,
            false,
            2025
        );

        $this->assertSame('85.00', $result['MATH'][5]['final']);
    }
}
