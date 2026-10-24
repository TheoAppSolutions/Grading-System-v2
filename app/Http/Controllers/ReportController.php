<?php

namespace App\Http\Controllers;

use App\Helpers\Formulas;
use App\Helpers\MyTools;
use App\Models\Award;
use App\Models\Enrollment;
use App\Services\SubjectExcellenceAwards;
use App\Support\ReportAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportController extends Controller
{
    public function generateQuarter($school_year_id, $student_id)
    {
        $record = Enrollment::with(['gradeLevel', 'schoolYear', 'student'])
            ->where('student_id', $student_id)
            ->where('school_year_id', $school_year_id)
            ->firstOrFail();

        abort_unless(ReportAccess::canViewEnrollment(Auth::user(), $record), 403);

        $data = $this->buildQuarterReportData($record);

        return $this->renderOrGeneratePdf(
            viewName: 'reports.'.strtolower(config('app.name')).'.quarter',
            data: $data,
            filename: "Report-{$student_id}.pdf",
            orientation: 'Portrait',
            scale: 0.65,
        );
    }

    public function generateQuarterBatch($school_year_id, $grade_level_id = null)
    {
        $schoolYearId = (int) $school_year_id;
        $gradeLevelId = filled($grade_level_id) ? (int) $grade_level_id : null;
        $user = Auth::user();

        abort_unless(ReportAccess::canAccessResource($user, $schoolYearId), 403);

        if ($gradeLevelId !== null) {
            abort_unless(ReportAccess::canViewGradeLevel($user, $schoolYearId, $gradeLevelId), 403);
        }

        $accessibleGradeLevelIds = ReportAccess::accessibleGradeLevelIds($user, $schoolYearId);
        $enrollments = Enrollment::query()
            ->with(['gradeLevel', 'schoolYear', 'student'])
            ->where('enrollments.school_year_id', $schoolYearId)
            ->whereHas('student', fn ($query) => $query->where('active', true))
            ->when(
                $gradeLevelId !== null,
                fn ($query) => $query->where('enrollments.grade_level_id', $gradeLevelId),
            )
            ->when(
                $accessibleGradeLevelIds !== null,
                fn ($query) => $query->whereIn('enrollments.grade_level_id', $accessibleGradeLevelIds ?: [-1]),
            )
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->select('enrollments.*')
            ->get();

        abort_if($enrollments->isEmpty(), 404, 'No enrolled students were found for the selected grade level.');

        $viewName = 'reports.'.strtolower(config('app.name')).'.quarter';
        abort_unless(View::exists($viewName), 404, "Report template [{$viewName}] was not found.");

        $reports = $enrollments
            ->map(function (Enrollment $enrollment) use ($viewName): string {
                $body = $this->renderReportCardBody(
                    View::make($viewName, $this->buildQuarterReportData($enrollment))->render(),
                );

                return '<section class="report-card__student-page">'.$body.'</section>';
            })
            ->implode('');

        return $this->renderBrowserPdf(
            html: '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
                .'<link href="'.e(url('assets/css/report_card.css')).'" rel="stylesheet">'
                .'</head><body class="report-card__batch">'.$reports.'</body></html>',
            filename: "Reports-{$schoolYearId}".($gradeLevelId ? "-{$gradeLevelId}" : '').'.pdf',
            orientation: 'Portrait',
            scale: 0.65,
        );
    }

    public function generateTOR($student_id)
    {
        abort_unless(ReportAccess::canViewStudentHistory(Auth::user()), 403);

        $type = 'semestral';
        $quarter = 2;

        $enrollments = Enrollment::where('student_id', $student_id)
            ->with(['gradeLevel', 'schoolYear', 'student'])
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

    public function generateAward($school_year_id, $student_id)
    {
        $record = Enrollment::with(['gradeLevel', 'schoolYear', 'student'])
            ->where('student_id', $student_id)
            ->where('school_year_id', $school_year_id)
            ->firstOrFail();

        abort_unless(ReportAccess::canViewEnrollment(Auth::user(), $record), 403);

        $this->syncSubjectAwards($record);
        $bucket = $this->buildEnrollmentBucket($record, 'semestral', 2, true);
        $awardData = $this->buildAwardData($record, $bucket['grades']);

        $data = [
            'grades' => $bucket['grades'],
            'gradeLevel' => $record->gradeLevel,
            'subjects' => $bucket['subjects'],
            'quarter' => 2,
            'type' => 'award',
            'student' => $record->student,
            'schoolYear' => $bucket['schoolYear'],
            'homeroom' => $bucket['homeroom'],
            'principal' => $bucket['principal'],
            'comments' => [],
            'attendance' => $this->attendanceForReport($record->student_id, $record->school_year_id),
            'award' => $awardData['award'],
        ];

        return $this->renderOrGeneratePdf(
            viewName: 'reports.'.strtolower(config('app.name')).'.award',
            data: $data,
            filename: "Award-{$student_id}.pdf"
        );
    }

    public function generateStudentAwardSummary($student_id)
    {
        abort_unless(ReportAccess::canViewStudentHistory(Auth::user()), 403);

        $enrollments = Enrollment::with(['gradeLevel', 'schoolYear', 'student'])
            ->where('student_id', $student_id)
            ->orderByDesc('school_year_id')
            ->get();

        abort_if($enrollments->isEmpty(), 404, 'No enrollment records were found for this student.');

        $records = [];

        foreach ($enrollments as $record) {
            $this->syncSubjectAwards($record);
            $bucket = $this->buildEnrollmentBucket($record, 'semestral', 2, true);
            $awardData = $this->buildAwardData($record, $bucket['grades']);

            $records[] = [
                'schoolYear' => $bucket['schoolYear'],
                'gradeLevel' => $record->gradeLevel,
                'award' => $awardData['award'],
                'perfectAttendance' => $awardData['perfectAttendance'],
            ];
        }

        return $this->renderOrGeneratePdf(
            viewName: 'reports.templates.summary.student_award',
            data: [
                'student' => $enrollments->first()->student,
                'records' => $records,
            ],
            filename: "Awards-{$student_id}.pdf"
        );
    }

    public function generateAwardSummary($school_year_id, $grade_level_id)
    {
        abort_unless(
            ReportAccess::canViewGradeLevel(Auth::user(), (int) $school_year_id, (int) $grade_level_id),
            403,
        );

        $enrollments = Enrollment::with(['gradeLevel', 'schoolYear', 'student'])
            ->where('school_year_id', $school_year_id)
            ->where('grade_level_id', $grade_level_id)
            ->orderBy('student_id')
            ->get();

        abort_if($enrollments->isEmpty(), 404, 'No enrolled students were found for this grade level.');

        $students = $enrollments->map(fn (Enrollment $enrollment) => $enrollment->student)
            ->filter()
            ->unique('id')
            ->values();
        $awards = [];
        $attendance = [];
        $homeroom = null;
        $principal = null;

        $this->syncSubjectAwards($enrollments->first());

        foreach ($enrollments->unique('student_id') as $enrollment) {
            $bucket = $this->buildEnrollmentBucket($enrollment, 'semestral', 2, true);
            $awardData = $this->buildAwardData($enrollment, $bucket['grades']);

            $homeroom ??= $bucket['homeroom'];
            $principal ??= $bucket['principal'];

            $awards[$enrollment->student_id] = $awardData['award'];
            $attendance[$enrollment->student_id] = $awardData['perfectAttendance'];
        }

        return $this->renderOrGeneratePdf(
            viewName: 'reports.templates.summary.award',
            data: [
                'gradelevel' => $enrollments->first()->gradeLevel->label,
                'schoolyear' => $enrollments->first()->schoolYear->label,
                'students' => $students,
                'awards' => $awards,
                'attendance' => $attendance,
                'homeroom' => $homeroom,
                'principal' => $principal,
            ],
            filename: "Awards-Summary-{$grade_level_id}.pdf",
            orientation: 'Landscape'
        );
    }

    /**
     * Build the grades/subjects bucket for a single enrollment record,
     * plus the parsed numeric school year.
     */
    private function buildEnrollmentBucket(
        Enrollment $enrollment,
        string $type,
        int $quarter,
        bool $isAwardReport = false
    ): array {
        $schoolYear = $enrollment->schoolYear->label;
        $schoolYearStartYear = $this->schoolYearStartYear($schoolYear);
        $isSemestral = $schoolYearStartYear >= 2025;
        $type = $isSemestral ? 'semestral' : 'quarter';
        $quarter = $isSemestral ? 2 : 4;
        $subjects = $enrollment->matchedSubjects()->get();

        $result = Formulas::buildGradesAndSubjects(
            $enrollment->student_id,
            $enrollment->grade_level_id,
            $enrollment->school_year_id,
            $subjects,
            $type,
            $quarter,
            $schoolYearStartYear,
            $isAwardReport
        );

        return [
            'schoolYear' => $schoolYear,
            'subjects' => $result['subjects'] ?? [],
            'grades' => $result['grades'] ?? [],
            'homeroom' => Formulas::getHomeroomAdvisor($enrollment->school_year_id, $enrollment->grade_level_id),
            'principal' => Formulas::getPrincipal(),
        ];
    }

    private function buildQuarterReportData(Enrollment $record): array
    {
        $type = 'semestral';
        $quarter = 2;
        $bucket = $this->buildEnrollmentBucket($record, $type, $quarter);

        return [
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
    }

    private function renderReportCardBody(string $html): string
    {
        $html = preg_replace('/<!DOCTYPE html>/i', '', $html) ?? $html;
        $html = preg_replace('/<head\b[^>]*>.*?<\/head>/is', '', $html) ?? $html;
        $html = preg_replace('/<\/?html\b[^>]*>|<\/?body\b[^>]*>/i', '', $html) ?? $html;

        return trim($html);
    }

    /**
     * Return the first calendar year from a school-year label.
     *
     * Report labels are commonly stored as "2026-2027", while the formula
     * helpers need the numeric start year to select the grading periods.
     */
    private function schoolYearStartYear(string|int $label): int
    {
        preg_match('/\d{4}/', (string) $label, $matches);

        return (int) ($matches[0] ?? 0);
    }

    private function renderOrGeneratePdf(
        string $viewName,
        array $data,
        string $filename,
        string $orientation = 'Portrait',
        float $scale = 1,
    ) {
        abort_unless(View::exists($viewName), 404, "Report template [{$viewName}] was not found.");

        return $this->renderBrowserPdf(
            html: View::make($viewName, $data)->render(),
            filename: $filename,
            orientation: $orientation,
            scale: $scale,
        );
    }

    private function renderBrowserPdf(
        string $html,
        string $filename,
        string $orientation = 'Portrait',
        float $scale = 1,
    ) {
        $html = $this->preparePdfHtml($html, $orientation, $scale);

        $browser = Browsershot::html($html)
            ->setNodeModulePath(base_path('node_modules'))
            ->format('A4')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->timeout(120);

        if ($nodeBinary = $this->findBrowsershotNodeBinary()) {
            $browser->setNodeBinary($nodeBinary);
        }

        if ($orientation === 'Landscape') {
            $browser->landscape();
        }

        $pdf = $browser->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
        ]);
    }

    private function preparePdfHtml(string $html, string $orientation, float $scale = 1): string
    {
        $pageSize = $orientation === 'Landscape' ? 'A4 landscape' : 'A4 portrait';
        $reportCardStyles = $scale !== 1.0 ? <<<HTML
    body:not(.report-card__batch) {
        min-height: 0;
    }

    body:not(.report-card__batch) > div {
        zoom: {$scale};
    }

    body.report-card__batch {
        display: block;
        min-height: 0;
    }

    .report-card__student-page {
        min-height: calc(297mm - 16mm);
        box-sizing: border-box;
        padding: 0;
        page-break-after: always;
        break-after: page;
    }

    .report-card__student-page:last-child {
        page-break-after: auto;
        break-after: auto;
    }

    .report-card__student-page > div {
        zoom: {$scale};
    }
HTML : '';
        $styles = <<<HTML
<style>
    @page {
        size: {$pageSize};
        margin: 8mm;
    }

    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    body > div {
        margin: 0 !important;
    }

    table {
        margin-left: auto;
        margin-right: auto;
    }
{$reportCardStyles}
</style>
HTML;

        if (stripos($html, '</head>') !== false) {
            return preg_replace('/<\/head>/i', $styles.'</head>', $html, 1) ?? $html;
        }

        return $styles.$html;
    }

    private function findBrowsershotNodeBinary(): ?string
    {
        if (filled($configuredBinary = config('browsershot.node_binary'))) {
            return $configuredBinary;
        }

        $candidates = [
            '/opt/homebrew/bin/node',
            '/usr/local/bin/node',
            '/usr/bin/node',
        ];

        if (filled($home = getenv('HOME'))) {
            $candidates[] = $home.'/Library/Application Support/Herd/config/nvm/versions/node/*/bin/node';
            $candidates[] = $home.'/.nvm/versions/node/*/bin/node';
        }

        foreach ($candidates as $candidate) {
            $matches = str_contains($candidate, '*') ? glob($candidate) : [$candidate];

            foreach ($matches ?: [] as $match) {
                if (is_executable($match)) {
                    return $match;
                }
            }
        }

        return null;
    }

    private function buildAwardData(Enrollment $enrollment, array $grades): array
    {
        $award = [
            'HIGHESTHONORS' => false,
            'HIGHHONORS' => false,
            'HONORS' => false,
            'DEPORTMENT' => false,
            'DEPORTMENT_grade' => data_get($grades, 'deportment.5', ''),
            'EFFORT' => false,
            'SEA' => [],
            'term-summary__average' => data_get($grades, 'term-summary__average', data_get($grades, 'generalaverage', '')),
        ];

        $average = (float) data_get($grades, 'generalaverage', 0);
        $disqualifiedForHonors = (bool) data_get($grades, 'disqualifyForHonors', false);

        if (! $disqualifiedForHonors && $average >= 96) {
            $award['HIGHESTHONORS'] = true;
        } elseif (! $disqualifiedForHonors && $average >= 93) {
            $award['HIGHHONORS'] = true;
        } elseif (! $disqualifiedForHonors && $average >= 90) {
            $award['HONORS'] = true;
        }

        $deportment = data_get($grades, 'deportment.5');
        $award['DEPORTMENT'] = $deportment !== null && (float) $deportment >= 90;
        $award['EFFORT'] = data_get($grades, 'disqualifyForEffort') === false;

        $awards = Award::with('subject')
            ->where('student_id', $enrollment->student_id)
            ->where('school_year_id', $enrollment->school_year_id)
            ->get();

        foreach ($awards as $record) {
            $subject = $record->subject;

            if (! $subject) {
                continue;
            }

            $title = trim((string) $subject->subject_title);
            $normalizedTitle = strtoupper(preg_replace('/\s+/', ' ', $title));
            $subjectCode = (string) $subject->subject_code;
            $mark = MyTools::checkMark();
            $award[$normalizedTitle] = $mark;
            $award[$subjectCode] = $mark;

            $grade = data_get($grades, $subjectCode.'.5.rawfinal', data_get($grades, $subjectCode.'.5.final', ''));
            $award['SEA'][] = [
                'subjecttitle' => $normalizedTitle,
                'subject_title' => $normalizedTitle,
                'grade' => $grade === '' ? '' : number_format((float) $grade, 2),
            ];
        }

        return [
            'award' => $award,
            'perfectAttendance' => $this->hasPerfectAttendance(
                $enrollment->student_id,
                $enrollment->school_year_id
            ),
        ];
    }

    /**
     * Ensure subject-excellence awards exist using the same rules as V1:
     * highest final average at 90+ per subject, with P.E. awarded separately
     * to the highest-scoring male and female student. Manual selections are
     * preserved by the service.
     */
    private function syncSubjectAwards(Enrollment $enrollment): void
    {
        app(SubjectExcellenceAwards::class)->syncForEnrollment($enrollment);
    }

    private function attendanceForReport(int $studentId, int $schoolYearId): array
    {
        $attendance = Formulas::getAttendance($studentId, $schoolYearId);
        $attendance['PerfectAttendance'] = $this->hasPerfectAttendance($studentId, $schoolYearId);

        return $attendance;
    }

    private function hasPerfectAttendance(int $studentId, int $schoolYearId): bool
    {
        $attendance = Formulas::getAttendance($studentId, $schoolYearId);

        if ($attendance === []) {
            return false;
        }

        foreach ($attendance as $period) {
            if (($period['absent'] ?? 0) > 0 || ($period['tardy'] ?? 0) > 0) {
                return false;
            }
        }

        return true;
    }
}
