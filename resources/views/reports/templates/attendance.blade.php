<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body{font:11px Arial,sans-serif;color:#111}h1,h2,p{text-align:center;margin:5px}table{width:100%;border-collapse:collapse;margin-top:18px}th,td{border:1px solid #444;padding:6px;text-align:center}th{background:#eee}.name{text-align:left}.summary{font-weight:bold;background:#f4f4f4}
    </style>
</head>
<body>
    <h1>Attendance Report</h1>
    <h2>{{ $gradeLevel->label }} · {{ $schoolYear->label }}</h2>
    <p>{{ $entryLabel }} · School days: {{ number_format($schoolDays, 1) }}</p>
    <table>
        <thead><tr><th>#</th><th class="name">Student</th><th>Section</th><th>Present</th><th>Absent</th><th>Tardy</th></tr></thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="name">{{ $row['student']?->full_name ?? '—' }}</td>
                <td>{{ $row['section'] ?? '—' }}</td>
                <td>{{ number_format($row['present'], 1) }}</td>
                <td>{{ number_format($row['absent'], 1) }}</td>
                <td>{{ number_format($row['tardy'], 1) }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No active students are enrolled in this grade level.</td></tr>
        @endforelse
        </tbody>
        @if($rows->isNotEmpty())
            <tfoot><tr class="summary"><td colspan="3">Class total</td><td>{{ number_format($rows->sum('present'), 1) }}</td><td>{{ number_format($rows->sum('absent'), 1) }}</td><td>{{ number_format($rows->sum('tardy'), 1) }}</td></tr></tfoot>
        @endif
    </table>
</body>
</html>
