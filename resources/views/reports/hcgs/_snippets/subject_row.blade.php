{{--
    Renders one subject's grade row.

    Expects:
        $subject - subject model/object with subject_code, subject_title
        $grades  - full grades array, keyed by subject_code
        $height  - row height style string, e.g. "height:17.0pt;"

    Note on the [1] / [2] / [5] keys below: these are period identifiers
    (1 = Quarter 1, 2 = Quarter 2, 5 = Semester/Final). Documented here since
    they aren't self-explanatory from the array access alone.
--}}
@php
    $code = $subject->subject_code;
    $q1Final   = data_get($grades, "$code.1.final");
    $q1Effort  = data_get($grades, "$code.1.effort");
    $q2Final   = data_get($grades, "$code.2.final");
    $q2Effort  = data_get($grades, "$code.2.effort");
    $rawFinal  = data_get($grades, "$code.5.rawfinal");
    $finalEffort = data_get($grades, "$code.5.effort");
    $unit      = data_get($grades, "$code.unit");
    $remarks   = data_get($grades, "$code.remarks");
@endphp
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=12 class=subjecttitle width=180>{{ $subject->subject_title }}</td>
    <td colspan=6 class=grade width=45>{{ $q1Final ?? '' }}</td>
    <td colspan=4 class=effort>{{ $q1Effort ?? '' }}</td>
    <td colspan=6 class=grade width=45>{{ $q2Final ?? '' }}</td>
    <td colspan=4 class=effort>{{ $q2Effort ?? '' }}</td>
    <td colspan=4 class=grade>{{ $rawFinal ?? '' }}</td>
    <td colspan=2 class=effort>{{ $finalEffort ?? '' }}</td>
    <td colspan=5 class=grade style="text-align: center !important;">{{ $unit ?? '' }}</td>
    <td colspan=7 class=remarks>{{ $remarks ?? '' }}</td>
    <td class=blank></td>
</tr>