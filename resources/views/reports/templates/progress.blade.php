<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Progress</title>
    <style>
        body{font:11px Arial,sans-serif;color:#111}h1,h2,p{text-align:center;margin:5px}h3{margin:18px 0 6px}table{width:100%;border-collapse:collapse;margin:8px 0 18px}th,td{border:1px solid #444;padding:5px;text-align:center}th{background:#eee}.name{text-align:left}.muted{color:#666}
    </style>
</head>
<body>
    <h1>Student Progress Record</h1>
    <h2>{{ $enrollment->student->full_name }}</h2>
    <p>{{ $enrollment->gradeLevel->label }} · School Year {{ $enrollment->schoolYear->label }}</p>
    <h3>Subject Grades</h3>
    <table>
        <thead><tr><th class="name">Subject</th><th>Q1</th><th>Q2</th><th>Q3</th><th>Q4</th><th>Mid-quarter</th></tr></thead>
        <tbody>
        @forelse($subjects as $subject)
            @php($subjectGrades = $grades->get($subject->id, collect())->keyBy('quarter'))
            <tr><td class="name">{{ $subject->subject_code }}</td>
                @foreach([1,2,3,4] as $quarter)<td>{{ $subjectGrades->get($quarter)?->final ?? '—' }}</td>@endforeach
                <td>{{ $subjectGrades->firstWhere('midquarter', '!=', null)?->midquarter ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No subjects assigned.</td></tr>
        @endforelse
        </tbody>
    </table>
    <h3>Attendance</h3>
    <table><thead><tr><th>Period</th><th>School days</th><th>Present</th><th>Absent</th><th>Tardy</th></tr></thead><tbody>
        @forelse($attendance as $period => $record)
            <tr><td>{{ $period }}</td><td>{{ $record['schooldays'] }}</td><td>{{ $record['present'] }}</td><td>{{ $record['absent'] }}</td><td>{{ $record['tardy'] }}</td></tr>
        @empty<tr><td colspan="5" class="muted">No attendance records.</td></tr>@endforelse
    </tbody></table>
    <h3>Homeroom Comments</h3>
    <table><thead><tr><th>Criteria</th><th>Quarter</th><th>Mid-quarter</th></tr></thead><tbody>
        @php($quarterComments = $comments['comments_quarter'] ?? [])
        @php($midquarterComments = $comments['comments_midquarter'] ?? [])
        @forelse(array_unique(array_merge(array_keys($quarterComments), array_keys($midquarterComments))) as $criteria)
            <tr><td>{{ $criteria }}</td><td>{{ is_scalar($quarterComments[$criteria] ?? null) ? ($quarterComments[$criteria] ?? '—') : json_encode($quarterComments[$criteria] ?? []) }}</td><td>{{ is_scalar($midquarterComments[$criteria] ?? null) ? ($midquarterComments[$criteria] ?? '—') : json_encode($midquarterComments[$criteria] ?? []) }}</td></tr>
        @empty<tr><td colspan="3" class="muted">No comments recorded.</td></tr>@endforelse
    </tbody></table>
</body>
</html>
