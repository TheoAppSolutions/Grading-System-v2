@php
    $selectedComments = $selectedComments ?? [];
    $checked = fn (string $key) => in_array($key, $selectedComments, true);
@endphp

<tr style='height: 17pt'>
    <td colspan=28 class=report-card__comments-title>ACADEMIC PERFORMANCE</td>
    <td class=report-card__blank></td>
    <td colspan=33 class=report-card__comments-title>CONDUCT</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('academic_1')])
    <td colspan=26 class=report-card__comments width=390>Grasps new ideas readily and making steady progress</td>
    <td class=report-card__blank width=15></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('conduct_1')])
    <td colspan=31 class=report-card__comments width=465>Works well in groups, planning and carrying out activities</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('academic_2')])
    <td colspan=26 class=report-card__comments>Keep up the great work!</td>
    <td class=report-card__blank></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('conduct_2')])
    <td colspan=31 class=report-card__comments>Respects the authority of those in charge of various</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('academic_3')])
    <td colspan=26 class=report-card__comments width=390>Inclined to hurry too much. This affects quality of work.</td>
    <td class=report-card__blank></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('conduct_3')])
    <td colspan=31 class=report-card__comments>Needs to listen and follow directions; has difficulty in concentrating</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('academic_4')])
    <td colspan=26 class=report-card__comments>Low report-card__grade affects his/her general average</td>
    <td class=report-card__blank></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('conduct_4')])
    <td colspan=31 class=report-card__comments>Needs to improve in self-control but seems unconcerned about it</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('academic_5')])
    <td colspan=26 class=report-card__comments>Has the ability but is not applying himself/herself</td>
    <td colspan=30 class=report-card__blank></td>
</tr>

@include('reports.templates.parts.blank_row')

<tr style='height: 17pt'>
    <td colspan=28 class=report-card__comments-title>ATTITUDE</td>
    <td class=report-card__blank></td>
    <td colspan=33 class=report-card__comments-title>NEEDING IMPROVEMENT</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('attitude_1')])
    <td colspan=26 class=report-card__comments>Is a steadfast, conscientious worker</td>
    <td class=report-card__blank></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('improvement_1')])
    <td colspan=31 class=report-card__comments>Does little work in school unless given full attention by the teacher</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('attitude_2')])
    <td colspan=26 class=report-card__comments>Performs well in everything he/she undertakes</td>
    <td class=report-card__blank></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('improvement_2')])
    <td colspan=31 class=report-card__comments>Not doing school work and seems not to care about doing</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('attitude_3')])
    <td colspan=26 class=report-card__comments>Doesn't volunteer often but is willing to take part</td>
    <td class=report-card__blank></td>
    <td colspan=2 class=report-card__blank>&nbsp;</td>
    <td colspan=31 class=report-card__comments>assignments; follow up is needed at home</td>
</tr>
<tr style='height: 17pt'>
    <td colspan=2 class=report-card__blank>&nbsp;</td>
    <td colspan=26 class=report-card__comments>in classwork when requested</td>
    <td class=report-card__blank></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('improvement_4')])
    <td colspan=31 class=report-card__comments>Needs to give full attention/concentration in all subject areas</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('attitude_5')])
    <td colspan=26 class=report-card__comments>Is learning to share his/her ideas</td>
    <td class=report-card__blank></td>
    @include('reports.templates.parts.check_mark', ['check' => $checked('improvement_5')])
    <td colspan=31 class=report-card__comments>Language/Reading handicap is the main source of difficulty.</td>
</tr>

@include('reports.templates.parts.blank_row')

@include('reports.templates.parts.attendance_section')

@include('reports.templates.parts.blank_row')

<tr style='height: 17pt'>
    <td colspan=42 class=report-card__comments-title>ATTENDANCE AND TARDY</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('attendance_1')])
    <td colspan=38 class=report-card__comments>Does good work when present but frequent absences cause difficulties.</td>
    <td colspan=20 class=report-card__student-info>{{ $homeroomAdviser ?? '' }}</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('attendance_2')])
    <td colspan=38 class=report-card__comments>Frequent absences affect the quality of work and assignments.</td>
    <td colspan=20 class=report-card__office-label>Homeroom Adviser</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('attendance_3')])
    <td colspan=40 class=report-card__comments>Is tardy frequently. This disturbs classmates and also affects his/her work.</td>
    @for ($i = 0; $i < 20; $i++)
        <td class=report-card__blank></td>
    @endfor
</tr>

@include('reports.templates.parts.blank_row')

<tr style='height: 17pt'>
    <td colspan=40 class=report-card__comments-title>OVERALL REQUEST</td>
    <td colspan=20 class=report-card__student-info>{{ $schoolPrincipal ?? '' }}</td>
</tr>
<tr style='height: 17pt'>
    @include('reports.templates.parts.check_mark', ['check' => $checked('overall_conference')])
    <td colspan=38 class=report-card__comments>A Parent-Teacher Conference is requested ASAP</td>
    <td colspan=20 class=report-card__office-label>School Principal</td>
</tr>

@include('reports.templates.parts.blank_row')

<tr style='height: 17pt'>
    <td colspan=7 class=report-card__sidenote-title>Eligible for:</td>
    <td colspan=12 class=report-card__student-info>{{ $eligibleFor ?? '&nbsp;' }}</td>
    @for ($i = 0; $i < 43; $i++)
        <td class=report-card__blank></td>
    @endfor
</tr>