<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Master Summary — {{ $gradeLevel->label }}</title>
    <style>
        body{font:10px Arial,sans-serif;color:#111}h1,p{text-align:center;margin:5px}table{width:100%;border-collapse:collapse;margin-top:14px}th,td{border:1px solid #444;padding:4px;text-align:center}th{background:#eee}.name{text-align:left;min-width:130px}.subject{writing-mode:vertical-rl;transform:rotate(180deg);height:110px;max-width:25px}.low{background:#ffe0e0}.effort{font-size:8px}
    </style>
</head>
<body>
    <h1>Master Grade Summary — {{ $gradeLevel->label }}</h1>
    <p>{{ $schoolYear->label }}</p>
    <table>
        <thead><tr><th>#</th><th class="name">Student</th>@foreach($subjects as $subject)<th class="subject">{{ $subject->subject_code }}</th>@endforeach</tr></thead>
        <tbody>
        @forelse($students as $student)
            <tr>
                <td>{{ $loop->iteration }}</td><td class="name">{{ $student->full_name }}</td>
                @foreach($subjects as $subject)
                    @php($subjectGrades = $grades->get($student->id, collect())->get($subject->id, collect()))
                    @php($completedGrades = $subjectGrades->whereIn('quarter', range(1, $periodCount))->whereNotNull('final'))
                    @php($average = $completedGrades->count() === $periodCount ? $completedGrades->avg('final') : null)
                    <td class="{{ $average !== null && $average < 70 ? 'low' : '' }}">{{ $average !== null ? number_format($average, 1) : '—' }}</td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ $subjects->count() + 2 }}">No active students enrolled in this grade level.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
