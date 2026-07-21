@if(isset($subjects[$ctr]))
    <td colspan=4 class=report-card__checkmark></td>
    <td colspan=10 class=report-card__comments>{{ $subjects[$ctr]["subjectname"] }}</td>
    <td class=report-card__blank></td>
@endif
