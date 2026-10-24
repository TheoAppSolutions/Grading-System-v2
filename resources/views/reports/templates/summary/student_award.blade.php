<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Awards Summary</title>
    <style>
        body {
            color: #111;
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 24px;
        }

        h1 {
            font-size: 18pt;
            margin: 0 0 4px;
            text-align: center;
        }

        .student-details {
            margin: 0 0 18px;
            text-align: center;
        }

        .school-year-section {
            break-inside: avoid;
            margin-top: 24px;
        }

        .school-year-section:first-of-type {
            margin-top: 0;
        }

        .school-year-heading {
            border-bottom: 1px solid #111;
            margin: 0 0 8px;
            padding-bottom: 4px;
        }

        .school-year-details {
            margin: 0 0 8px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #111;
            padding: 8px 10px;
            vertical-align: middle;
        }

        th {
            background: #f0f0f0;
            text-align: left;
        }

        .status {
            text-align: center;
            width: 90px;
        }

        .checkmark {
            height: 20px;
            width: 20px;
        }
    </style>
</head>
<body>
    <h1>Awards Summary</h1>
    <p class="student-details">
        <strong>{{ $student->last_name }}, {{ $student->first_name }}</strong>
    </p>

    @foreach ($records as $record)
        @php($award = $record['award'])
        @php($hasStandardAward = collect(['HIGHESTHONORS', 'HIGHHONORS', 'HONORS', 'DEPORTMENT', 'EFFORT'])->contains(fn ($key) => !empty($award[$key])))
        @php($hasAwards = $hasStandardAward || !empty($record['perfectAttendance']) || !empty($award['SEA']))

        @if ($hasAwards)
            <section class="school-year-section">
                <h2 class="school-year-heading">School Year: {{ $record['schoolYear'] }}</h2>
                <p class="school-year-details">
                    <strong>Grade Level:</strong> {{ data_get($record['gradeLevel'], 'label', '') }}
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>Award</th>
                            <th>Subject / Details</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                @foreach([
                    'HIGHESTHONORS' => 'With Highest Honors',
                    'HIGHHONORS' => 'With High Honors',
                    'HONORS' => 'With Honors',
                    'DEPORTMENT' => 'Deportment',
                    'EFFORT' => 'Effort',
                ] as $key => $label)
                    @if (!empty($award[$key]))
                        <tr>
                            <td>{{ $label }}</td>
                            <td>
                                <img class="checkmark" src="{{ url('assets/images/checkmark.png') }}" alt="✓">
                                {{ $key === 'DEPORTMENT' ? 'Final deportment rating' : ($key === 'EFFORT' ? 'All required effort ratings' : 'Academic excellence') }}
                            </td>
                            <td>{{ $key === 'DEPORTMENT' ? ($award['DEPORTMENT_grade'] ?? '') : ($key === 'HONORS' || $key === 'HIGHHONORS' || $key === 'HIGHESTHONORS' ? ($award['term-summary__average'] ?? '') : '') }}</td>
                        </tr>
                    @endif
                @endforeach
                @if (!empty($record['perfectAttendance']))
                    <tr>
                        <td>Perfect Attendance</td>
                        <td>
                            <img class="checkmark" src="{{ url('assets/images/checkmark.png') }}" alt="✓">
                            School year attendance
                        </td>
                        <td></td>
                    </tr>
                @endif
                @foreach ($award['SEA'] ?? [] as $subjectAward)
                    <tr>
                        <td>Subject Excellence Award</td>
                        <td>
                            <img class="checkmark" src="{{ url('assets/images/checkmark.png') }}" alt="✓">
                            {{ $subjectAward['subjecttitle'] ?? $subjectAward['subject_title'] ?? '' }}
                        </td>
                        <td>{{ $subjectAward['grade'] ?? '' }}</td>
                    </tr>
                @endforeach
                    </tbody>
                </table>
            </section>
        @endif
    @endforeach
</body>
</html>
