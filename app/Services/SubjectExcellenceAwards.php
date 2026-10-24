<?php

namespace App\Services;

use App\Models\Award;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubjectExcellenceAwards
{
    /**
     * Ensure SEA records exist for every subject assigned to an enrollment's
     * grade level and school year without overwriting manual selections.
     */
    public function syncForEnrollment(Enrollment $enrollment): void
    {
        $subjects = $enrollment->matchedSubjects()->get()->unique('id');
        $schoolYearLabel = $enrollment->schoolYear?->label;

        foreach ($subjects as $subject) {
            $this->syncForSubject(
                subject: $subject,
                schoolYearId: (int) $enrollment->school_year_id,
                gradeLevelId: (int) $enrollment->grade_level_id,
                schoolYearLabel: $schoolYearLabel,
            );
        }
    }

    /**
     * Return completed final averages for the subject's enrolled students.
     * V1 used all available students for the subject and displayed the top
     * ten; callers can limit the returned collection for display while the
     * synchronizer still sees every eligible student (important for P.E.,
     * where the male and female winners are calculated independently).
     */
    public function ranking(
        Subject $subject,
        int $schoolYearId,
        int $gradeLevelId,
        ?string $schoolYearLabel = null,
    ): Collection {
        $periodCount = $this->periodCount($schoolYearLabel ?? SchoolYear::query()->find($schoolYearId)?->label);
        $enrollments = Enrollment::query()
            ->where('school_year_id', $schoolYearId)
            ->where('grade_level_id', $gradeLevelId)
            ->whereHas('student', fn ($query) => $query->where('active', true))
            ->with('student')
            ->get();

        $grades = Grade::query()
            ->where('school_year_id', $schoolYearId)
            ->where('grade_level_id', $gradeLevelId)
            ->where('subject_id', $subject->id)
            ->get()
            ->groupBy('student_id');
        $selectedStudentIds = Award::query()
            ->where('subject_id', $subject->id)
            ->where('school_year_id', $schoolYearId)
            ->pluck('student_id')
            ->map(static fn ($studentId): string => (string) $studentId)
            ->all();

        return $enrollments
            ->map(function (Enrollment $enrollment) use ($grades, $periodCount, $selectedStudentIds): ?array {
                $score = $this->finalSubjectGrade(
                    $grades->get($enrollment->student_id, collect()),
                    $periodCount,
                );

                if ($score === null || ! $enrollment->student) {
                    return null;
                }

                return [
                    'student_id' => $enrollment->student_id,
                    'student' => $enrollment->student,
                    'score' => $score,
                    'gender' => $enrollment->student->gender,
                    'eligible' => $score >= 90,
                    'selected' => in_array((string) $enrollment->student_id, $selectedStudentIds, true),
                ];
            })
            ->filter()
            ->sort(function (array $left, array $right): int {
                $scoreComparison = $right['score'] <=> $left['score'];

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                return strcasecmp(
                    $left['student']->last_name.$left['student']->first_name,
                    $right['student']->last_name.$right['student']->first_name,
                );
            })
            ->values();
    }

    /**
     * Manually select a student for SEA. P.E. keeps one selection per gender;
     * all other subjects keep one selection for the subject and school year.
     */
    public function selectForStudent(
        Subject $subject,
        int $schoolYearId,
        int $gradeLevelId,
        int $studentId,
        ?string $schoolYearLabel = null,
    ): void {
        $entry = $this->ranking($subject, $schoolYearId, $gradeLevelId, $schoolYearLabel)
            ->firstWhere('student_id', $studentId);

        if (! $entry || ! $entry['eligible']) {
            throw new \InvalidArgumentException('The selected student is not eligible for a Subject Excellence Award.');
        }

        DB::transaction(function () use ($subject, $schoolYearId, $entry): void {
            $awards = Award::query()
                ->where('subject_id', $subject->id)
                ->where('school_year_id', $schoolYearId);

            if ($this->isPhysicalEducation($subject)) {
                $awards->where('gender', $entry['gender']);
            }

            $awards->delete();

            Award::create([
                'student_id' => $entry['student_id'],
                'subject_id' => $subject->id,
                'school_year_id' => $schoolYearId,
                'gender' => $entry['gender'] ?? '',
            ]);
        });
    }

    private function syncForSubject(
        Subject $subject,
        int $schoolYearId,
        int $gradeLevelId,
        ?string $schoolYearLabel,
    ): void {
        $ranking = $this->ranking($subject, $schoolYearId, $gradeLevelId, $schoolYearLabel);
        $eligible = $ranking->where('eligible', true);
        $isPhysicalEducation = $this->isPhysicalEducation($subject);
        $existingGenders = Award::query()
            ->where('subject_id', $subject->id)
            ->where('school_year_id', $schoolYearId)
            ->pluck('gender')
            ->map(static fn ($gender): string => strtoupper((string) $gender))
            ->all();

        if (! $isPhysicalEducation && $existingGenders !== []) {
            return;
        }

        $winnerIds = $isPhysicalEducation
            ? collect(['M', 'F'])
                ->reject(fn (string $gender): bool => in_array($gender, $existingGenders, true))
                ->flatMap(
                    fn (string $gender): Collection => $this->highestScoringIds($eligible->where('gender', $gender))
                )
            : $this->highestScoringIds($eligible);

        DB::transaction(function () use ($subject, $schoolYearId, $winnerIds, $ranking): void {
            foreach ($winnerIds as $studentId) {
                if (Award::query()
                    ->where('student_id', $studentId)
                    ->where('subject_id', $subject->id)
                    ->where('school_year_id', $schoolYearId)
                    ->exists()) {
                    continue;
                }

                $student = $ranking->firstWhere('student_id', $studentId);

                Award::create([
                    'student_id' => $studentId,
                    'subject_id' => $subject->id,
                    'school_year_id' => $schoolYearId,
                    'gender' => $student['gender'] ?? '',
                ]);
            }
        });
    }

    private function highestScoringIds(Collection $scores): Collection
    {
        $highest = $scores->max('score');

        return $scores
            ->where('score', $highest)
            ->pluck('student_id')
            ->values();
    }

    private function finalSubjectGrade(Collection $grades, int $periodCount): ?float
    {
        $gradesByQuarter = $grades->keyBy('quarter');
        $finals = [];

        for ($quarter = 1; $quarter <= $periodCount; $quarter++) {
            $grade = $gradesByQuarter->get($quarter);

            if (! $grade || $grade->final === null || $grade->final === '') {
                return null;
            }

            $finals[] = (float) $grade->final;
        }

        return array_sum($finals) / count($finals);
    }

    private function periodCount(?string $schoolYearLabel): int
    {
        preg_match('/\d{4}/', (string) $schoolYearLabel, $matches);

        return (int) ($matches[0] ?? 0) >= 2025 ? 2 : 4;
    }

    private function isPhysicalEducation(Subject $subject): bool
    {
        $normalized = strtoupper(preg_replace('/[^A-Z0-9]+/i', '', (string) $subject->subject_title));

        return in_array($normalized, ['PE', 'PHYSICALEDUCATION'], true);
    }
}
