<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mid-quarter Report</title>
    <style>
        body{font:12px Arial,sans-serif;color:#111}h1,h2,p{text-align:center;margin:5px}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #444;padding:7px;text-align:left}th{background:#eee}.numeric{text-align:center;width:110px}
    </style>
</head>
<body>
    <h1>Mid-quarter Progress Report</h1>
    <h2>{{ $enrollment->student->full_name }}</h2>
    <p>{{ $enrollment->gradeLevel->label }} · School Year {{ $enrollment->schoolYear->label }}</p>
    <table>
        <thead><tr><th>Subject</th><th class="numeric">Mid-quarter grade</th><th>Teacher remarks</th></tr></thead>
        <tbody>
        @forelse($subjects as $subject)
            @php($grade = $grades->get($subject->id))
            <tr><td>{{ $subject->subject_code }}</td><td class="numeric">{{ $grade?->midquarter ?? '—' }}</td><td>{{ $grade?->remarks ?? '' }}</td></tr>
        @empty
            <tr><td colspan="3">No subjects are assigned for this enrollment.</td></tr>
        @endforelse
        </tbody>
    </table>
    <h2>Homeroom Comments</h2>
    <table>
        <thead><tr><th>Criteria</th><th>Selected comment</th></tr></thead>
        <tbody>
        @forelse($comments as $criteriaId => $selected)
            <tr>
                <td>{{ $commentCriteria->get($criteriaId)?->category?->label ?? 'Criteria '.$criteriaId }}</td>
                <td>
                    @php($selectedValues = is_array($selected) ? $selected : [$selected])
                    {{ collect($selectedValues)->map(fn ($value) => $commentCriteria->get($value)?->description ?? $value)->implode(', ') }}
                </td>
            </tr>
        @empty
            <tr><td colspan="2">No homeroom comments recorded.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
