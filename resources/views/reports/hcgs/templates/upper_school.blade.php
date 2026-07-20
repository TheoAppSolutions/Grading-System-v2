<html lang="en">
<head>
    <title>{{ $student->last_name . ', ' . $student->first_name }}</title>
    <meta charset="UTF-8">
    @include('reports.hcgs._snippets.report_card_styles')
</head>
<body>
@php($height = "height:17.0pt;")
<div>
    <table border=0 cellpadding=0 cellspacing=0 width=930
           style='border-collapse:collapse;table-layout:fixed;width:682pt'>
        <col class=blank width=15 span=7 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=2 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=2 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=2 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=3 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=3 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=7 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=3 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=2 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=5 style='width:11pt'>
        <col class=blank width=15 style='width:11pt'>
        <col class=blank width=15 span=5 style='width:11pt'>
        <col class=blank width=15 span=2 style='width:18pt'>
        <col class=blank width=15 span=8 style='width:18pt'>

        <tr style="position: relative;">
            <td colspan=62 style="text-align: center; vertical-align: middle; position: relative; height: 0;">
                <img src="{{ url('assets/images/logo.png') }}"
                    style="height: 100px; position: absolute; left: 120px;"/>
            </td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader width=930>HIGHLANDS CHRISTIAN GRAMMAR SCHOOL</td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader2 width=930>Mt. Hagen, WHP</td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader2>Papua New Guinea</td>
        </tr>

        @include('reports.hcgs._snippets.blank_row', ['height' => $height])

        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader>PROGRESS REPORT</td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=schoolyear>School Year {{ $schoolYear ?? '' }}</td>
        </tr>

        @include('reports.hcgs._snippets.blank_row', ['height' => $height])

        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=4 class=studentlabel>Name:</td>
            <td colspan=20 class=studentinfo>{{ $student->last_name . ', ' . $student->first_name }}</td>
            <td class=blank></td>
            <td colspan=4 class=studentlabel>Gender:</td>
            <td colspan=2 class=studentinfo>{{ $student->gender }}</td>
            @for ($i = 0; $i < 9; $i++)
                <td class=blank></td>
            @endfor
            <td colspan=4 class=studentlabel>Semester:</td>
            <td colspan=2 class=studentinfo>{{ $quarter ?? '' }}</td>
            <td class=blank></td>
            <td colspan=7 class=studentlabel>Year Level:</td>
            <td colspan=4 class=studentinfo>{{ $gradeLevel ?? '' }}</td>
        </tr>

        @include('reports.hcgs._snippets.blank_row', ['height' => $height])

        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=12 rowspan=2 height=48 class=cardlabel width=180>SUBJECTS
            </td>
            <td colspan=20 class=cardlabel>SEMESTER</td>
            <td colspan=6 rowspan=2 class=cardlabel width=90 style='width:66pt'>FINAL<br>
                GRADE
            </td>
            <td colspan=5 rowspan=2 class=cardlabel>UNITS</td>

            <td colspan=7 rowspan=2 class=cardlabel>REMARKS</td>
            <td class=blank></td>
            <td colspan=20 rowspan="16">
                @include('reports.hcgs._snippets.card_score_board')
            </td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=10 class=term>1</td>
            <td colspan=10 class=term>2</td>
            <td class=blank></td>
        </tr>

        {{--
            Fixed curriculum: this report card always shows this exact subject
            list regardless of what's in the student's enrollment/subjects
            relation, so each row is an explicit @include rather than a
            @foreach over $subjects. Only the `grades.<key>` lookup for each
            row is dynamic — swap the second argument in each 'code' below to
            match your actual subject_code / grade lookup key per subject.
        --}}
        @php
            $subjectRows = [
                ['title' => 'Agriculture', 'key' => 'AGRICULTURE'],
                ['title' => 'The Arts', 'key' => 'ARTS'],
                ['title' => 'Business Studies', 'key' => 'BUSINESS STUDIES'],
                ['title' => 'Civics/Geography', 'key' => 'CIVICS'],
                ['title' => 'CRE', 'key' => 'CRE'],
                ['title' => 'English', 'key' => 'ENGLISH'],
                ['title' => 'Personal Development (Health)', 'key' => 'HEALTH'],
                ['title' => 'History', 'key' => 'HISTORY'],
                ['title' => 'ICT/Practical Skills', 'key' => 'ICT/PRACTICAL SKILLS'],
                ['title' => 'Making A Living/Home Economics', 'key' => 'LIVING/HOME ECONOMICS'],
                ['title' => 'Mathematics', 'key' => 'MATHEMATICS'],
                ['title' => 'Physical Education', 'key' => 'P.E.'],
                ['title' => 'Science', 'key' => 'SCIENCE'],
                ['title' => 'Social Science', 'key' => 'SOCIAL SCIENCE'],
                ['title' => 'Elective (Music Private Lessons)', 'key' => 'MUSIC'],
            ];
        @endphp

        @foreach ($subjectRows as $row)
            @include('reports.hcgs._snippets.fixed_subject_row', [
                'title' => $row['title'],
                'subject' => $subjects[$row['key']] ?? '',
                'grades' => $grades,
                'height' => $height,
            ])
        @endforeach

        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=12 class=subjecttitle width=180>DEPORTMENT
            </td>
            <td colspan=10 class=deportment width=75>{{ data_get($grades, 'deportment.1') }}</td>
            <td colspan=10 class=deportment width=75>{{ data_get($grades, 'deportment.2') }}</td>
            <td colspan=6 class=attendance>{{ data_get($grades, 'deportment.5') }}</td>
            <td colspan=4 class=grade></td>
            <td colspan=1 class=effort></td>
            <td colspan=7 class=remarks></td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=32 class=cardlabel width=480 style="text-align: right;">GENERAL AVERAGE
            </td>
            <td colspan=6 class=attendance>{{ $grades['generalaverage'] ?? '' }}</td>
            <td colspan=5 class=deportment>{{ $grades['totalunits'] ?? '' }}</td>
            <td colspan=7 class=remarks>{{ $grades['generalremarks'] ?? '' }}</td>
        </tr>

        @include('reports.hcgs._snippets.blank_row', ['height' => $height])

        @include('reports.hcgs._snippets.comments_section', [
            'height' => $height,
            'selectedComments' => $selectedComments ?? [],
            'homeroomAdviser' => $homeroomAdviser ?? '',
            'schoolPrincipal' => $schoolPrincipal ?? '',
            'attendance' => $attendance ?? [],
            'eligibleFor' => $eligibleFor ?? '',
        ])
    </table>
</div>
</body>
</html>