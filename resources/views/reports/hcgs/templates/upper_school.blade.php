@extends('reports.hcgs._layouts.quarter')

@php
    $height = "height:17.0pt;";
@endphp

@section('content')
    @php
        // Split into core subjects vs electives once, instead of looping
        // the full subject list twice with opposite conditions.
        [$electives, $coreSubjects] = collect($subjects)->partition(
            fn ($subject) => str_contains((string) $subject->subject_code, 'Elective')
        );
    @endphp

    @foreach ($coreSubjects as $subject)
        @include('reports.hcgs._snippets.subject_row', [
            'subject' => $subject,
            'grades' => $grades,
            'height' => $height,
        ])
    @endforeach

    @foreach ($electives as $subject)
        @include('reports.hcgs._snippets.subject_row', [
            'subject' => $subject,
            'grades' => $grades,
            'height' => $height,
        ])
    @endforeach

    <tr style='mso-height-source:userset;{{ $height }}'>
        <td colspan=12 class=subjecttitle width=180>DEPORTMENT
        </td>
        <td colspan=10 class=deportment width=75>{{ data_get($grades, 'deportment.1', '') }}</td>
        <td colspan=10 class=deportment width=75>{{ data_get($grades, 'deportment.2', '') }}</td>
        <td colspan=6 class=attendance>{{ data_get($grades, 'deportment.5', '') }}</td>
        <td colspan=4 class=grade></td>
        <td colspan=1 class=effort></td>
        <td colspan=7 class=remarks></td>
    </tr>
    <tr style='mso-height-source:userset;{{ $height }}'>
        <td colspan=32 class=cardlabel width=480 style="text-align: right;">GENERAL AVERAGE
        </td>
        <td colspan=6 class=attendance>{{ $grades['generalaverage'] ?? '' }}</td>
        <td colspan=5 class=deportment>{{ $grades['totalunits'] ?? '' }}</td>
        <td colspan=7 class=remarks>{{ $grades['generalremarks'] ?? '' }}</td>
    </tr>
@endsection