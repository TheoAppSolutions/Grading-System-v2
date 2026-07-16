<html lang="en">
<head>
    <title>{{ $student->last_name . ', ' . $student->first_name }}</title>
    <meta charset="UTF-8">
    {{-- If your PDF engine specifically requires windows-1252, swap the line above for:
         <meta http-equiv="Content-Type" content="text/html; charset=windows-1252"> --}}
    @include('reports.hcgs._snippets.styles')
</head>
<body>
<div align=center>
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

        {{-- School identity: pull from config/school record instead of hardcoding,
             so this template can be reused for other schools if needed. --}}
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader width=930>{{ $school->name ?? config('reportcards.school.name', 'HIGHLANDS CHRISTIAN GRAMMAR SCHOOL') }}</td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader2 width=930>{{ $school->address_line1 ?? config('reportcards.school.address_line1', 'Mt. Hagen, WHP') }}</td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader2>{{ $school->address_line2 ?? config('reportcards.school.address_line2', 'Papua New Guinea') }}</td>
        </tr>

        @include('reports.hcgs._snippets.blank_row')

        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=cardheader>PROGRESS
                REPORT
            </td>
        </tr>
        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=62 class=schoolyear>School Year
                {{ $records['school_years']['label'] ?? '' }}
            </td>
        </tr>

        @include('reports.hcgs._snippets.blank_row')

        <tr style='mso-height-source:userset;{{ $height }}'>
            <td colspan=4 class=studentlabel>Name:</td>
            <td colspan=20 class=studentinfo>{{ $student->last_name . ', ' . $student->first_name }}</td>
            <td class=blank></td>
            <td colspan=4 class=studentlabel>Gender:</td>
            <td colspan=2 class=studentinfo>{{ $student->gender }}</td>
            <td class=blank></td>
            <td class=blank></td>
            <td class=blank></td>
            <td class=blank></td>
            <td class=blank></td>
            <td class=blank></td>
            <td class=blank></td>
            <td class=blank></td>
            <td class=blank></td>
            @if ($quarter != "award")
                <td colspan=6 class=studentlabel>Semester:</td>
                {{-- Was previously defaulting silently to 2 when $quarter was missing.
                     Now surfaces a visible placeholder instead of a misleading value. --}}
                <td colspan=2 class=studentinfo>{{ $quarter ?? 'N/A' }}</td>
            @else
                <td colspan=4 class=blank></td>
                <td colspan=2 class=blank></td>
            @endif

            <td class=blank></td>
            <td colspan=7 class=studentlabel>Year Level:</td>
            <td colspan=4 class=studentinfo>{{ $gradeLevel ?? '' }}</td>
        </tr>

        @include('reports.hcgs._snippets.blank_row')

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

        @yield('content')

        @include('reports.hcgs._snippets.blank_row')

        @if($quarter == "award")
            @include('reports.hcgs._snippets.footer_award')
        @else
            {{-- @include('reports.hcgs._snippets.footer_quarter') --}}
        @endif
    </table>
</div>
</body>
</html>