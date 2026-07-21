{{--
    Renders one subject row where the SUBJECT ITSELF is fixed (hardcoded title),
    but the GRADES are pulled live from the reports controller's $grades array.

    Use this instead of looping a $subjects collection when the curriculum for
    this report card is a fixed list rather than the student's enrolled subjects.

    Expects:
        $title  - display master-summary__name shown on the card, e.g. "Agriculture"
        $code   - the key used to look up this subject inside $grades,
                  e.g. "agriculture". MUST match whatever key the controller
                  used when building $grades[$code][...] (adjust to your
                  actual subject_code / lookup key).
        $grades - full grades array, same shape as the rest of the report:
                  $grades[$code][1]['final'|'report-card__effort'], [2][...], [5]['rawfinal'|'report-card__effort'],
                  $grades[$code]['unit'], $grades[$code]['report-card__remarks']
        $height - row height style string, e.g. "height:17.0pt;"
        $not_applicable - boolean to indicate if the subject is not applicable
--}}
<?php
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
?>
<tr style='height: 17pt'>
    <td colspan=12 class=report-card__subject-title width=180>{{ $title }}</td>
    <td colspan=6 class=report-card__grade width=45>{{ $q1Final ?? '' }}</td>
    <td colspan=4 class=report-card__effort>{{ $q1Effort ?? '' }}</td>
    <td colspan=6 class=report-card__grade width=45>{{ $q2Final ?? '' }}</td>
    <td colspan=4 class=report-card__effort>{{ $q2Effort ?? '' }}</td>
    <td colspan=4 class=report-card__grade>{{ $rawFinal ?? '' }}</td>
    <td colspan=2 class=report-card__effort>{{ $finalEffort ?? '' }}</td>
    <td colspan=5 class=report-card__grade style="text-align: center !important;">{{ $unit ?? '' }}</td>
    <td colspan=7 class=report-card__remarks>{{ $remarks ?? '' }}</td>
    <td class=report-card__blank></td>
</tr>