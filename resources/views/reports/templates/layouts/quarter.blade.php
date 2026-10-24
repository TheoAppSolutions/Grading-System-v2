<html lang="en">
<head>
    <title>{{ $student->last_name . ', ' . $student->first_name }}</title>
    <meta charset="UTF-8">
    {{-- If your PDF engine specifically requires windows-1252, swap the line above for:
         <meta http-equiv="Content-Type" content="text/html; charset=windows-1252"> --}}
    <link href="{{ url('assets/css/report_card.css') }}" rel="stylesheet">
</head>
<body>
<div align=center>
    <table border=0 cellpadding=0 cellspacing=0 width=930
           style='border-collapse:collapse;table-layout:fixed;width:682pt'>
        <col class=report-card__blank width=15 span=7 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=2 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=2 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=2 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=3 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=3 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=7 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=3 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=2 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=5 style='width:11pt'>
        <col class=report-card__blank width=15 style='width:11pt'>
        <col class=report-card__blank width=15 span=5 style='width:11pt'>
        <col class=report-card__blank width=15 span=2 style='width:18pt'>
        <col class=report-card__blank width=15 span=8 style='width:18pt'>

        <tr style="position: relative;">
            <td colspan=62 class="report-card__logo">
                <img src="{{ url('assets/images/logo.png') }}" class="report-card__image"/>
            </td>
        </tr>

        @yield('header-section')

        <tr style="height: 17pt;">
            <td colspan=62 class=report-card__header>PROGRESS REPORT</td>
        </tr>
        <tr style="height: 17pt;">
            <td colspan=62 class=report-card__school-year>School Year {{ $schoolYear }}</td>
        </tr>

        @include('reports.templates.parts.blank_row')

        <tr style="height: 17pt;">
            <td colspan=4 class=report-card__student-label>Name:</td>
            <td colspan=20 class=report-card__student-info>{{ $student->last_name . ', ' . $student->first_name }}</td>
            <td class=report-card__blank></td>
            <td colspan=4 class=report-card__student-label>Gender:</td>
            <td colspan=2 class=report-card__student-info>{{ $student->gender }}</td>
            <td colspan=9 class=report-card__blank></td>

            @yield('header-semester')

            <td class=report-card__blank></td>
            <td colspan=7 class=report-card__student-label>Year Level:</td>
            <td colspan=4 class=report-card__student-info>{{ $gradeLevel->label ?? '' }}</td>
        </tr>

        @include('reports.templates.parts.blank_row')

        <tr style="height: 17pt;">
            <td colspan=12 rowspan=2 height=48 class=report-card__card-label width=180>SUBJECTS</td>
            <td colspan=20 rowspan=1 class=report-card__card-label>SEMESTER</td>
            <td colspan=6 rowspan=2 class=report-card__card-label width=90 style='width:66pt'>FINAL<br>GRADE</td>
            <td colspan=5 rowspan=2 class=report-card__card-label>UNITS</td>

            <td colspan=7 rowspan=2 class=report-card__card-label>REMARKS</td>
            <td class=report-card__blank></td>
            <td colspan=20 rowspan="16">
                @include('reports.templates.parts.card_score_board')
            </td>
        </tr>
        <tr style="height: 17pt;">
            <td colspan=10 class=report-card__term>1</td>
            <td colspan=10 class=report-card__term>2</td>
            <td class=report-card__blank></td>
        </tr>

        @yield('content')

        @include('reports.templates.parts.blank_row')

        @if (($type ?? null) === 'award')
            @include('reports.templates.parts.footer_award', ['subjects' => array_values($subjects ?? [])])
        @endif
    </table>
</div>
</body>
</html>
