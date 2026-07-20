{{--
    Comments, attendance, and signature block. The comment TEXT is fixed —
    it's a standard list of stock remarks printed on every card — so this is
    deliberately written as explicit rows rather than a @foreach. Only the
    checkmarks, names, and attendance numbers vary per student.

    Expects:
        $height           - row height style string, e.g. "height:17.0pt;"
        $selectedComments - array of comment keys to show a checkmark for,
                             e.g. ['academic_1', 'conduct_1']. Adjust the key
                             names below to match however your controller
                             actually stores which stock comments were picked.
        $homeroomAdviser  - e.g. "Ms. BEVELYNNE PORA"
        $schoolPrincipal  - e.g. "Mr. JONAH DIKINSEP"
        $attendance       - ['schooldays' => [1 => .., 2 => .., 'total' => ..],
                              'present'    => [1 => .., 2 => .., 'total' => ..],
                              'absent'     => [1 => .., 2 => .., 'total' => ..],
                              'tardy'      => [1 => .., 2 => .., 'total' => ..]]
        $eligibleFor       - e.g. "Honor Roll" or blank
--}}
@php
    $selectedComments = $selectedComments ?? [];
    $checked = fn (string $key) => in_array($key, $selectedComments, true);
@endphp

<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=28 class=commentstitle>ACADEMIC
        PERFORMANCE
    </td>
    <td class=blank></td>
    <td colspan=33 class=commentstitle>CONDUCT</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark width=30>
        @if ($checked('academic_1'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments width=390>Grasps new ideas readily and making steady progress</td>
    <td class=blank width=15></td>
    <td colspan=2 class=checkmark width=30>
        @if ($checked('conduct_1'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments width=465>Works well in groups, planning and carrying out activities</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('academic_2'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments>Keep up the great work!</td>
    <td class=blank></td>
    <td colspan=2 class=checkmark>
        @if ($checked('conduct_2'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments>Respects the authority of those in charge of various</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('academic_3'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments width=390>Inclined to hurry too much. This affects quality of work.</td>
    <td class=blank></td>
    <td colspan=2 class=checkmark>
        @if ($checked('conduct_3'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments>Needs to listen and follow directions; has difficulty in concentrating</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('academic_4'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments>Low grade affects his/her general average</td>
    <td class=blank></td>
    <td colspan=2 class=checkmark>
        @if ($checked('conduct_4'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments>Needs to improve in self-control but seems unconcerned about it</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('academic_5'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments>Has the ability but is not applying himself/herself</td>
    <td class=blank></td>
    <td class=blank></td><td class=blank></td><td class=blank></td><td class=blank></td>
    <td class=blank></td><td class=blank></td><td class=blank></td><td class=blank></td>
    <td class=blank></td><td class=blank></td><td class=blank></td><td class=blank></td>
    <td class=blank></td><td class=blank></td><td class=blank></td><td class=blank></td>
    <td class=blank></td><td class=blank></td><td class=blank></td><td class=blank></td>
    <td class=blank></td><td class=blank></td><td class=blank></td><td class=blank></td>
    <td class=blank></td><td class=blank></td><td class=blank></td><td class=blank></td>
    <td class=blank></td>
</tr>

@include('reports.hcgs._snippets.blank_row', ['height' => $height])

<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=28 class=commentstitle>ATTITUDE</td>
    <td class=blank></td>
    <td colspan=33 class=commentstitle>NEEDING IMPROVEMENT</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('attitude_1'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments>Is a steadfast, conscientious worker</td>
    <td class=blank></td>
    <td colspan=2 class=checkmark>
        @if ($checked('improvement_1'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments>Does little work in school unless given full attention by the teacher</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('attitude_2'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments>Performs well in everything he/she undertakes</td>
    <td class=blank></td>
    <td colspan=2 class=checkmark>
        @if ($checked('improvement_2'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments>Not doing school work and seems not to care about doing</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('attitude_3'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments>Doesn't volunteer often but is willing to take part</td>
    <td class=blank></td>
    <td colspan=2 class=blank>&nbsp;</td>
    <td colspan=31 class=comments>assignments; follow up is needed at home</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=blank>&nbsp;</td>
    <td colspan=26 class=comments>in classwork when requested</td>
    <td class=blank></td>
    <td colspan=2 class=checkmark>
        @if ($checked('improvement_4'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments>Needs to give full attention/concentration in all subject areas</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('attitude_5'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=26 class=comments>Is learning to share his/her ideas</td>
    <td class=blank></td>
    <td colspan=2 class=checkmark>
        @if ($checked('improvement_5'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=31 class=comments>Language/Reading handicap is the main source of difficulty.</td>
</tr>

@include('reports.hcgs._snippets.blank_row', ['height' => $height])

<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=8 class=months>Semester</td>
    <td colspan=4 class=months>1</td>
    <td colspan=4 class=months>2</td>
    <td colspan=4 class=months>Total</td>
    <td class=blank></td>
    <td class=blank></td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=8 class=attendancelabel>School Days</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'schooldays.1') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'schooldays.2') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'schooldays.total', 0) }}</td>
    <td class=blank></td>
    <td class=blank></td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=8 class=attendancelabel>Days Present</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'present.1') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'present.2') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'present.total', 0) }}</td>
    <td class=blank></td>
    <td class=blank></td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=8 class=attendancelabel>Days Absent</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'absent.1') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'absent.2') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'absent.total', 0) }}</td>
    <td class=blank></td>
    <td class=blank></td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=8 class=attendancelabel>Days Tardy</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'tardy.1') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'tardy.2') }}</td>
    <td colspan=4 class=attendance>{{ data_get($attendance, 'tardy.total', 0) }}</td>
    <td class=blank></td>
    <td class=blank></td>
</tr>

@include('reports.hcgs._snippets.blank_row', ['height' => $height])

<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=42 class=commentstitle>ATTENDANCE
        AND TARDY
    </td>
    @for ($i = 0; $i < 20; $i++)
        <td class=blank></td>
    @endfor
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('attendance_1'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=38 class=comments>Does good work when present but frequent absences cause difficulties.</td>
    <td colspan=20 class=studentinfo>{{ $homeroomAdviser ?? '' }}</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('attendance_2'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=38 class=comments>Frequent absences affect the quality of work and assignments.</td>
    <td colspan=20 class=officelabel>Homeroom Adviser</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('attendance_3'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=40 class=comments>Is tardy frequently. This disturbs classmates and also affects his/her work.</td>
    @for ($i = 0; $i < 20; $i++)
        <td class=blank></td>
    @endfor
</tr>

@include('reports.hcgs._snippets.blank_row', ['height' => $height])

<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=40 class=commentstitle>OVERALL
        REQUEST
    </td>
    <td colspan=20 class=studentinfo>{{ $schoolPrincipal ?? '' }}</td>
</tr>
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=2 class=checkmark>
        @if ($checked('overall_conference'))<img src='{{ asset('checkmark.png') }}' style='height:20px;width:20px;'/>@endif
    </td>
    <td colspan=38 class=comments>
        A Parent-Teacher Conference is requested ASAP
    </td>
    <td colspan=20 class=officelabel>School Principal</td>
</tr>

@include('reports.hcgs._snippets.blank_row', ['height' => $height])

<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=7 class=sidenotetitle>Eligible for:</td>
    <td colspan=12 class=studentinfo>{{ $eligibleFor ?? '&nbsp;' }}</td>
    @for ($i = 0; $i < 43; $i++)
        <td class=blank></td>
    @endfor
</tr>