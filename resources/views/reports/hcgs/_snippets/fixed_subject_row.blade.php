{{--
    Renders one subject row where the SUBJECT ITSELF is fixed (hardcoded title),
    but the GRADES are pulled live from the reports controller's $grades array.

    Use this instead of looping a $subjects collection when the curriculum for
    this report card is a fixed list rather than the student's enrolled subjects.

    Expects:
        $title  - display name shown on the card, e.g. "Agriculture"
        $code   - the key used to look up this subject inside $grades,
                  e.g. "agriculture". MUST match whatever key the controller
                  used when building $grades[$code][...] (adjust to your
                  actual subject_code / lookup key).
        $grades - full grades array, same shape as the rest of the report:
                  $grades[$code][1]['final'|'effort'], [2][...], [5]['rawfinal'|'effort'],
                  $grades[$code]['unit'], $grades[$code]['remarks']
        $height - row height style string, e.g. "height:17.0pt;"
        $not_applicable - boolean to indicate if the subject is not applicable
--}}
@php
    $na          = data_get($subject, "applicable_flg");
    $code        = data_get($subject, "subject_code");
    $q1Final     = data_get($grades, "$code.1.final");
    $q1Effort    = data_get($grades, "$code.1.effort");
    $q2Final     = data_get($grades, "$code.2.final");
    $q2Effort    = data_get($grades, "$code.2.effort");
    $rawFinal    = data_get($grades, "$code.5.rawfinal");
    $finalEffort = data_get($grades, "$code.5.effort");
    $unit        = data_get($grades, "$code.unit");
    $remarks     = data_get($grades, "$code.remarks") ?? ($na ? 'N / A' : '');
@endphp
<tr style='mso-height-source:userset;{{ $height }}'>
    <td colspan=12 class=subjecttitle width=180>{{ $title }}</td>
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