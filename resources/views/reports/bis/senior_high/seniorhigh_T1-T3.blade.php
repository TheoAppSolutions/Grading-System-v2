@extends('reports.templates._layouts.T1-T3')

@section('content')
    <tr class=report-card__blank style='height:15.0pt'>
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
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td colspan=16 height=20 class=midquarter-report__teachers width=384 style='height:15.0pt;width:288pt'>SUBJECT</td>
        <td colspan=7 class=midquarter-report__teachers width=168 style='width:126pt'>TEACHER</td>
        <td colspan=4 class=midquarter-report__teachers width=96 style='width:72pt'>MQ Grade</td>
        <td colspan=4 class=midquarter-report__teachers width=96 style='width:72pt'>Performance</td>
        <td colspan=4 class=midquarter-report__teachers width=96 style='width:72pt'>Behavior</td>
        <td colspan=13 class=midquarter-report__teachers width=310 style='width:232pt'>Comments</td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td colspan=84 height=20 class=term-summary__subjects style="font-weight: bold;">Core Subjects</td>
    </tr>
    @foreach($core as $subject)
        @php($grades["$subject->subjectname"]['teacher'] = $subject->title . ' ' . $subject->firstname . ' ' . $subject->lastname)
        @if (isset($grades["$subject->subjectname"]['teacher']))
            <tr class=report-card__blank style='height:15.0pt'>
                <td colspan=16 height=20 class=term-summary__subjects>{{ $subject->subjecttitle ?? '' }}</td>
                <td colspan=7
                    class=term-summary__subjects>{{ $grades["$subject->subjectname"]['teacher'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $grades["$subject->subjectname"][$quarter]['midquarter'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['performance'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['behavior'] ?? '' }}</td>
                <td colspan=13 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['additional'] ?? '' }}</td>
            </tr>
        @endif
    @endforeach
    <tr class=report-card__blank style='height:15.0pt'>
        <td colspan=84 height=20 class=term-summary__subjects style="font-weight: bold;">Applied and Specialized Subjects</td>
    </tr>
    @foreach($specialized as $subject)
        @php($grades["$subject->subjectname"]['teacher'] = $subject->title . ' ' . $subject->firstname . ' ' . $subject->lastname)
        @if (isset($grades["$subject->subjectname"]['teacher']))
            <tr class=report-card__blank style='height:15.0pt'>
                <td colspan=16 height=20 class=term-summary__subjects>{{ $subject->subjecttitle ?? '' }}</td>
                <td colspan=7 class=term-summary__subjects>{{ $grades["$subject->subjectname"]['teacher'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $grades["$subject->subjectname"][$quarter]['midquarter'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['performance'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['behavior'] ?? '' }}</td>
                <td colspan=13 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['additional'] ?? '' }}</td>
            </tr>
        @endif
    @endforeach
    @foreach($applied as $subject)
        @php($grades["$subject->subjectname"]['teacher'] = $subject->title . ' ' . $subject->firstname . ' ' . $subject->lastname)
        @if (isset($grades["$subject->subjectname"]['teacher']))
            <tr class=report-card__blank style='height:15.0pt'>
                <td colspan=16 height=20 class=term-summary__subjects>{{ $subject->subjecttitle ?? '' }}</td>
                <td colspan=7 class=term-summary__subjects>{{ $grades["$subject->subjectname"]['teacher'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $grades["$subject->subjectname"][$quarter]['midquarter'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['performance'] ?? '' }}</td>
                <td colspan=4 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['behavior'] ?? '' }}</td>
                <td colspan=13 class=midquarter-report__teachers
                    style="vertical-align: middle;">{{ $comments["$subject->subjectname"]['additional'] ?? '' }}</td>
            </tr>
        @endif
    @endforeach

    <tr class=report-card__blank style='height:15.0pt'>
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
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td colspan=3 height=20 class=report-card__blank style='height:15.0pt'>Legend:</td>
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
        <td colspan=4 class=report-card__blank>Comments:</td>
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
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td class=report-card__blank></td>
        <td colspan=18 class=report-card__blank>
            MQ Grade: 75 - 100, grades below 75 is reported as Failed
        </td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__remarks width=24 style='width:18pt'>1</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Works for academic excellence
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td class=report-card__blank></td>
        <td colspan=18 class=report-card__blank width=432 style='width:324pt'>
            Performance and Behavior: reported as
        </td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>2</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Shows academic improvement
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td class=report-card__blank></td>
        <td colspan=18 class=report-card__blank style="padding-left: 20px;"><b>E</b> (Excellent)</td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>3</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Participates effectively
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td class=report-card__blank></td>
        <td colspan=18 class=report-card__blank style="padding-left: 20px;"><b>S</b> (Satisfactory)</td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>4</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Positive attitude and report-card__effort
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td class=report-card__blank></td>
        <td colspan=18 class=report-card__blank style="padding-left: 20px;"><b>U</b> (Unsatisfactory)</td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>5</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Makes connections and applies knowledge
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td class=report-card__blank></td>
        <td colspan=18 class=report-card__blank>
            For Comments: please refer to the table on the right:
        </td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>6</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Displays good study skills
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>7</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Works well in group activities
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>8</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Appears disorganized
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>9</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Quality of work declining
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>10</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Does not bring materials/follow instructions
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td class=report-card__school-year colspan="8" rowspan="3">
            {!! signatureTeacher($homeroom_id) !!}
        </td>
        <td class=report-card__blank></td>
        <td class=report-card__school-year colspan="8" rowspan="3">
            {!! signatureMiss() !!}
        </td>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>11</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Inconsistent report-card__effort
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>12</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Needs to participate more in discussions
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>13</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Difficulty in making connections and applying knowledge
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td colspan=8 class=report-card__sidenote-title>{{ $homeroom ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=8 class=report-card__sidenote-title>{{ $principal ?? '' }}</td>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>14</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Has difficulty in exams
        </td>
        <td class=report-card__blank></td>
    </tr>
    <tr class=report-card__blank style='height:15.0pt'>
        <td height=20 class=report-card__blank style='height:15.0pt'></td>
        <td colspan=8 class=term-summary__undertitle>Homeroom Teacher</td>
        <td class=report-card__blank></td>
        <td colspan=8 class=term-summary__undertitle>Pricipal</td>
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
        <td class=report-card__remarks width=24 style='border-top:none;width:18pt'>15</td>
        <td colspan=18 class=term-summary__subjects width=431 style='width:323pt'>
            Unable to maintain attentiveness throughout entire class period
        </td>
        <td class=report-card__blank></td>
    </tr>
@endsection
