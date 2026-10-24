@if(isset($subjects[$ctr]))
    @php
        $subject = $subjects[$ctr];
        $subjectName = strtoupper(trim((string) ($subject->subject_title ?? $subject->subjectname ?? '')));
        $subjectCode = (string) ($subject->subject_code ?? '');
    @endphp
    <td colspan=4 class=report-card__checkmark>{!! $award[$subjectName] ?? $award[$subjectCode] ?? '' !!}</td>
    <td colspan=10 class=report-card__comments>{{ $subject->subject_title ?? $subject->subjectname ?? '' }}</td>
    <td class=report-card__blank></td>
@endif
