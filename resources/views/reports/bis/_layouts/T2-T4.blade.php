<?php
    use App\Helpers\MyTools;
?>
<html>
<head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    <title>Quarter Report Card</title>

    <link href="{{ url('ReportCard.css') }}" rel="stylesheet">
</head>
<body>
<div>
    <table class=report-card__blank
           style='border-collapse:collapse;table-layout:fixed;width:685pt'>
        <col class=report-card__blank span=7 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=2 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=2 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=2 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=3 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=3 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=7 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=3 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=2 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=5 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=5 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=2 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>
        <col class=report-card__blank span=9 style='mso-width-source:userset;mso-width-alt:512;width:11pt'>

        <tr style='height:15.0pt'>
            <td colspan="22" height="60" rowspan="4" style="text-align: right">
                <img src="{{ public_path('assets/images/logo.png') }}" style="height: 60.0pt;"/>
            </td>
            <td colspan=41 height=20 class=term-summary__label--heavy width=882 style='height:15.0pt;width:693pt;; text-align: left; padding-left: 0px;'>
                BETHEL INTERNATIONAL SCHOOL
            </td>
        </tr>
        <tr style='height:15.0pt'>
            <td colspan=41 height=20 class=term-summary__label--light style='height:15.0pt; text-align: left; padding-left: 50px;'>PAWING, PALO, LEYTE</td>
        </tr>
        <tr style='height:15.0pt'>
            <td colspan=41 height=20 class=term-summary__label--light style='height:15.0pt; text-align: left; padding-left: 90px;'>Philippines</td>
        </tr>
        <tr style='height:15.0pt'>
            <td colspan=41 height=20 class=term-summary__label--heavy style='height:15.0pt'></td>
        </tr>
        <tr style='height:15.0pt'>
            <td colspan=63 height=20 class=term-summary__label--heavy style='height:15.0pt'>REPORT CARD</td>
        </tr>
        <tr style='height:15.0pt'>
            <td colspan=63 height=20 class=term-summary__label--heavy style='height:15.0pt'>School Year
                {{ $schoolYear }}
            </td>
        </tr>

        @if(isset($semester) and $semester != '' and $student->gradelevel >= 11 and $student->gradelevel <= 12)
            <tr style='height:13.5pt'>
                <td height=20 class=report-card__blank style='height:13.5pt'></td>
            </tr>
            <tr style='height:15.0pt'>
                <td colspan=63 height=20 class=term-summary__label--heavy style='height:15.0pt'>{{ 'Semester ' . $semester }}</td>
            </tr>
        @endif

        <tr style='height:15.0pt'>
            <td colspan=63 height=20 class=report-card__blank style='height:15.0pt'></td>
        </tr>
        <tr style='height:18.75pt'>
            <td colspan=4 height=25 class=report-card__student-label style='height:18.75pt'>NAME:</td>
            <td colspan=20 class=report-card__student-info style="{{ strlen($student->last_name . ', ' . $student->first_name) >= 30 ? "font-size:13px" : "" }}">
                {{ $student->last_name . ', ' . $student->first_name }} {{ $student->suffix ?? "" }}
            </td>
            <td class=report-card__blank></td>
            <td colspan=3 class=report-card__student-label>LRN:</td>
            <td colspan=10 class=report-card__student-info>{{ $student->student_lrn ?? '' }}</td>
            <td class=report-card__blank></td>
            <td colspan=6 class=report-card__student-label>GENDER:</td>
            <td colspan=2 class=report-card__student-info>{{ $student->gender }}</td>
            <td class=report-card__blank></td>
            <td colspan=5 class=report-card__student-label>GRADE:</td>
            <td colspan={{ $student->section!='' ?'8' : '3' }} class=report-card__student-info>{{ $gradeLevel }} {{ $student->section_or_strand !='' ? ' - ' . $student->section_or_strand : '' }}</td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
        </tr>
        <tr style='height:13.5pt'>
            <td height=20 class=report-card__blank style='height:13.5pt'></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
        </tr>

        @yield('content')

        <tr style='height:15.0pt'>
            <td height=20 class=report-card__blank style='height:15.0pt'></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
            <td class=report-card__blank></td>
        </tr>

        @if ($type == "award")
            @include('reports.bis._partials.footer_award')
        @else
            {{-- @include('reports.bis._partials.footer_quarter') --}}
        @endif

    </table>
</div>
</body>
</html>
