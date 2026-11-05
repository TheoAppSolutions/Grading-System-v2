<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Subject Summary — {{ $subject->subject_code }}</title>
    <style>
        body{font:12px Arial,sans-serif;color:#111}h1,h2,p{text-align:center;margin:5px}table{width:100%;border-collapse:collapse;margin-top:18px}th,td{border:1px solid #444;padding:5px;text-align:center}th{background:#eee}.name{text-align:left}.low{background:#ffe0e0}.empty{color:#777}
    </style>
</head>
<body>
    <h1>{{ $subject->subject_code }} — {{ $subject->subject_title ?? $subject->subject_code }}</h1>
    <h2>{{ $subject->gradeLevel?->label }} · {{ $schoolYear->label }}</h2>
    <p>Subject teacher: {{ $subject->subjectUser->first()?->teacher?->name ?? 'Not assigned' }}</p>
    <table>
        <thead><tr><th>#</th><th class="name">Student</th>@foreach(range(1, $periodCount) as $quarter)<th>Q{{ $quarter }}</th>@endforeach<th>Final</th><th>Effort</th><th>Remarks</th></tr></thead>
        <tbody>
        @forelse($students as $student)
            @php($studentGrades = $grades->get($student->id, collect()))
            <tr>
                <td>{{ $loop->iteration }}</td><td class="name">{{ $student->full_name }}</td>
                @foreach(range(1, $periodCount) as $quarter)
                    @php($grade = $studentGrades->get($quarter))
                    <td class="{{ $grade?->final !== null && $grade->final < 70 ? 'low' : '' }}">{{ $grade?->final ?? '—' }}</td>
                @endforeach
                @php($completedGrades = $studentGrades->whereIn('quarter', range(1, $periodCount))->whereNotNull('final'))
                @php($finalGrade = $completedGrades->count() === $periodCount ? $completedGrades->avg('final') : null)
                <td>{{ $finalGrade !== null ? number_format($finalGrade, 2) : '—' }}</td>
                @php($efforts = $studentGrades->whereIn('quarter', range(1, $periodCount))->whereNotNull('effort'))
                <td>{{ $efforts->count() === $periodCount ? number_format($efforts->avg('effort'), 2) : '—' }}</td>
                <td>{{ $finalGrade === null ? '—' : ($finalGrade >= 60 ? 'PASSED' : 'FAILED') }}</td>
            </tr>
        @empty
            <tr><td colspan="{{ $periodCount + 5 }}" class="empty">No active students are enrolled in this grade level.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
