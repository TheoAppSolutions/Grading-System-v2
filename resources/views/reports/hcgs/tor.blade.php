<html lang="en">
<head>
    <title>{{ $student->last_name . ', ' . $student->first_name }}</title>
    <meta charset="UTF-8">
    <link href="{{ url('assets/css/report_card.css') }}" rel="stylesheet">
</head>
<body>
    <div style="position: relative;">
        <img src="{{ url('assets/images/logo.png') }}" alt="" style="height: 100px; width: 80px; position: absolute; left: 200px;">

        <table class=report-card__blank style='border-collapse:collapse;table-layout:fixed;'>
            @for($i=0; $i<38; $i++)
                <col class="report-card__blank" span=40 style='width:15pt'>
            @endfor

            <tr>
                <td colspan="42">
                    <table class=report-card__blank style="border-collapse:collapse;table-layout:fixed; width: 100%;">
                        <tr>
                            <td colspan="40" height="28" class="header header__school-name">HIGHLANDS CHRISTIAN GRAMMAR SCHOOL</td>
                        </tr>
                        <tr>
                            <td colspan="40" class="header header__address" style='width:555pt'>P.O. Box 1582</td>
                        </tr>
                        <tr>
                            <td colspan="40" class="header header__address">Mt. Hagen, WHP 281 Papua New Guinea</td>
                        </tr>
                        <tr>
                            <td height="10" class="report-card__blank"></td>
                        </tr>
                        <tr>
                            <td colspan="40" class="header header__moto">A Christian Learning Community</td>
                        </tr>
                        <tr>
                            <td colspan="40" class="header header__phone">Phone / Fax: (675) 7630 2494</td>
                        </tr>

                        <tr>
                            <td height="10" class="report-card__blank"></td>
                        </tr>
                        <tr>
                            <td height="10" class="report-card__blank"></td>
                        </tr>

                        <tr>
                            <td height="10" class="report-card__blank"></td>
                            <td colspan="8" class="student__details">{{ $student->last_name }}</td>
                            <td colspan="8" class="student__details">{{ $student->first_name }}</td>
                            <td colspan="7" class="student__details">{{ $student->middle_name }}</td>
                            <td colspan="3" class="student__details">{{ $student->suffix }}</td>
                            <td colspan="6" class="student__details">{{ $student->citizenship ?? '' }}</td>
                            <td colspan="6" class="student__details">{{ $student->id }}</td>
                            <td height="10" class="report-card__blank"></td>
                        </tr>
                        <tr>
                            <td height="10" class="report-card__blank"></td>
                            <td colspan="8" class="student__label">Last Name</td>
                            <td colspan="8" class="student__label">First Name</td>
                            <td colspan="7" class="student__label">Middle Name</td>
                            <td colspan="7" class="student__label">Suffix</td>
                            <td colspan="2" class="student__label"></td>
                            <td colspan="6" class="student__label">Student ID</td>
                            <td height="10" class="report-card__blank"></td>
                        </tr>

                        <tr>
                            <td height="10" class="report-card__blank"></td>
                        </tr>

                        <tr>
                            <td height="10" class="report-card__blank"></td>
                            <td colspan="8" class="student__details">{{ $student->gender == "M" ? "MALE" : "FEMALE" }}</td>
                            <td colspan="8" class="student__details">{{ $student->info->birth_date ?? '' }}</td>
                            <td colspan="16" class="student__details">{{ $student->info->address ?? '' }}</td>
                            <td colspan="6" class="student__details">{{ $schoolyears[0] ?? '' }}</td>
                            <td height="10" class="report-card__blank"></td>
                        </tr>

                        <tr>
                            <td height="10" class="report-card__blank"></td>
                            <td colspan="8" class="student__label">Gender</td>
                            <td colspan="8" class="student__label">Date of Birth</td>
                            <td colspan="16" class="student__label">Address</td>
                            <td colspan="6" class="student__label">Date of Entrance</td>
                            <td height="10" class="report-card__blank"></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td height="10" class="report-card__blank"></td>
            </tr>

            <tr>
                <td height="10" class="report-card__blank"></td>
                <td colspan="40" class="header header__tor-label">Official Academic Transcript</td>
                <td height="10" class="report-card__blank"></td>
            </tr>

            <tr>
                <td height="10" class="report-card__blank"></td>
            </tr>

            @php($ctr = 1)
            @foreach($records as $record)
                @php($grades = $record['grades'])
                @if($ctr==1)
                    {!! "<tr>" !!}
                    <td height="10" class="grades__report-card__blank"></td>
                @endif

                <td colspan="18" style="vertical-align: top;">
                    <table class="grades__table">
                        <tr>
                            <td height="10" class="grades__report-card__blank"></td>
                            <td class="grades__report-card__blank"></td>
                            <td class="grades__report-card__blank"></td>
                            <td class="grades__report-card__blank"></td>
                            <td class="grades__report-card__blank"></td>
                            <td colspan="3" class="grades__label">Year Level:</td>
                            <td colspan="3" class="grades__label">{{ $record['gradeLevel'] ?? '' }}</td>
                            <td colspan="3" class="grades__label">School Year:</td>
                            <td colspan="3" class="grades__label">{{ $record['schoolYear'] }}
                            </td>
                        </tr>
                        <tr>
                            <td height="10" class="grades__report-card__blank"></td>
                            <td colspan="4" rowspan="2" class="grades__label">SUBJECTS</td>
                            <td colspan="3" rowspan="2" class="grades__label">Semester<br>1</td>
                            <td colspan="3" rowspan="2" class="grades__label">Semester<br>2</td>
                            <td colspan="3" rowspan="2" class="grades__label">FINAL<br>GRADE</td>
                            <td colspan="3" rowspan="2" class="grades__label">LETTER<br>GRADE</td>
                            <td colspan="2" rowspan="2" class="grades__label">UNITS</td>
                            <td colspan="2" rowspan="2" class="grades__label">GRADE<br>POINTS</td>
                        </tr>
                        <tr>
                            <td height="10" class="grades__report-card__blank"></td>
                        </tr>

                        @foreach($record['subjects'] as $subjectTitle => $subject)
                            {{-- A matched subject may not have any grade rows yet. --}}
                            @php($row = $grades[$subject->subjectname] ?? [])
                            @if(isset($row[1]["final"]) or isset($row[2]["final"]) or isset($row[3]["final"]) or isset($row[4]["final"]))
                                <tr>
                                    <td height="10" class="grades__report-card__blank"></td>
                                    <td colspan="4" class="grades__subject" style="white-space: normal;">{{ $subject->subject_title }}</td>
                                    @if(isset($row[3]["final"]) or isset($row[4]["final"]))
                                        @if(isset($row[1]["final"]) and $row[1]["final"] > 0 and isset($row[2]["final"]) and $row[2]["final"] > 0)
                                            <td colspan="3" class="grades__final">{{ \App\Helpers\Formulas::average($row[1]["final"], $row[2]["final"]) ?? '' }}</td>
                                        @else
                                            <td colspan="3" class="grades__final"></td>
                                        @endif

                                        @if(isset($row[3]["final"]) and $row[3]["final"] > 0 and isset($row[4]["final"]) and $row[4]["final"] > 0)
                                            <td colspan="3" class="grades__final">{{ \App\Helpers\Formulas::average($row[3]["final"], $row[4]["final"]) ?? '' }}</td>
                                        @else
                                            <td colspan="3" class="grades__final"></td>
                                        @endif
                                    @else
                                        @if(isset($row[1]["final"]) and $row[1]["final"] > 0)
                                            <td colspan="3"
                                                class="grades__final">{{ $row[1]["final"] }}</td>
                                        @else
                                            <td colspan="3" class="grades__final"></td>
                                        @endif

                                        @if(isset($row[2]["final"]) and $row[2]["final"] > 0)
                                            <td colspan="3" class="grades__final">{{ $row[2]["final"] }}</td>
                                        @else
                                            <td colspan="3" class="grades__final"></td>
                                        @endif
                                    @endif

                                    <td colspan="3" class="grades__final">{{ $row[5]["final"] ?? "" }}</td>
                                    <td colspan="3" class="grades__final">
                                        @isset($row[5]["final"])
                                            {{ \App\Helpers\Formulas::getFinalLetterGrade($row[5]["final"]) }}
                                        @endisset
                                    </td>
                                    <td colspan="2" class="grades__final">{{ $row["unit"] ?? "" }}</td>
                                    <td colspan="2" class="grades__label">
                                        @isset($row["grade_point"])
                                            {{ $row["grade_point"] }}
                                        @endisset
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        <tr>
                            <td height="10" class=report-card__grades__report-card__blank>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="10" class="grades__report-card__blank"></td>
                            <td colspan="10" class="grades__average-label">Weighted General Average:</td>
                            <td colspan="3" class="grades__average-value">
                                @isset($grades['term-summary__average'])
                                    {{ $grades['term-summary__average'] }}
                                @endisset
                            </td>
                        </tr>
                        <tr>
                            <td height="10" class="grades__report-card__blank"></td>
                            <td colspan="10" class="grades__average-label">Weighted GPA:</td>
                            <td colspan="3" class="grades__average-value">
                                @isset($grades['term-summary__average'])
                                    {{ $grades['totalgpa'] }}
                                @endisset
                            </td>
                        </tr>
                        <tr>
                            <td height="10" class="grades__report-card__blank"></td>
                            <td colspan="10" class="grades__average-label">Total Units:</td>
                            <td colspan="3" class="grades__average-value">{{ $grades['totalunits'] ?? "" }}</td>
                        </tr>
                        <tr>
                            <td height="10" class="grades__report-card__blank"></td>
                            <td colspan="10" class="grades__average-label">Remarks:</td>
                            <td colspan="3" class="grades__average-value">{{ $grades['generalremarks'] ?? "" }}</td>
                        </tr>
                    </table>
                </td>

                @if($ctr==1)
                    <td class="grades__report-card__blank">&nbsp;</td>
                    <td class="grades__report-card__blank">&nbsp;</td>
                @endif

                @if($ctr==2)
                    <td height="10" class="grades__report-card__blank"></td>
                    {!! "</tr>" !!}
                    <tr>
                        <td height="10" class=report-card__grades__report-card__blank>&nbsp;</td>
                    </tr>
                    @php($ctr = 0)
                @endif
                @if(count($records) <= 1)
                    <td colspan="18" style="vertical-align: top;"></td>
                @endif
                @php($ctr += 1)
            @endforeach
        </table>
    </div>
</body>
</html>
