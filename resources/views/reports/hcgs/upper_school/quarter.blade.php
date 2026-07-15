@php($height = "height:17.0pt;")

@extends('reports.hcgs._layouts.quarter')

@section('content')
    @foreach($subjects as $subject)
        @if(strpos($subject->subject_code, 'Elective') === false)
            <tr style='mso-height-source:userset;{!! $height !!}'>
                <td colspan=12 class=subjecttitle width=180>{{ $subject->subject_title }}</td>
                <td colspan=6 class=grade width=45>{{ $grades[$subject->subject_code][1]['final'] ?? '' }}</td>
                <td colspan=4 class=effort>{{ $grades[$subject->subject_code][1]['effort'] ?? '' }}</td>
                <td colspan=6 class=grade width=45>{{ $grades[$subject->subject_code][2]['final'] ?? '' }}</td>
                <td colspan=4 class=effort>{{ $grades[$subject->subject_code][2]['effort'] ?? '' }}</td>
                <td colspan=4 class=grade>{{ $grades[$subject->subject_code][5]['rawfinal'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject->subject_code][5]['effort'] ?? '' }}</td>
                <td colspan=5 class=grade
                    style="text-align: center !important;">{{ $grades[$subject->subject_code]['unit'] ?? '' }}</td>
                <td colspan=7 class=remarks>{{ $grades[$subject->subject_code]['remarks'] ?? '' }}</td>
                <td class=blank></td>
            </tr>
        @endif
    @endforeach

    @foreach($subjects as $subject)
        @if(strpos($subject->subject_code, 'Elective') !== false)
            <tr style='mso-height-source:userset;{!! $height !!}'>
                <td colspan=12 class=subjecttitle width=180>{{ $subject->subject_title }}</td>
                <td colspan=6 class=grade width=45>{{ $grades[$subject->subject_code][1]['final'] ?? '' }}</td>
                <td colspan=4 class=effort>{{ $grades[$subject->subject_code][1]['effort'] ?? '' }}</td>
                <td colspan=6 class=grade width=45>{{ $grades[$subject->subject_code][2]['final'] ?? '' }}</td>
                <td colspan=4 class=effort>{{ $grades[$subject->subject_code][2]['effort'] ?? '' }}</td>
                <td colspan=4 class=grade>{{ $grades[$subject->subject_code][5]['rawfinal'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject->subject_code][5]['effort'] ?? '' }}</td>
                <td colspan=5 class=grade
                    style="text-align: center !important;">{{ $grades[$subject->subject_code]['unit'] ?? '' }}</td>
                <td colspan=7 class=remarks>{{ $grades[$subject->subject_code]['remarks'] ?? '' }}</td>
                <td class=blank></td>
            </tr>
        @endif
    @endforeach

    <tr style='mso-height-source:userset;{!! $height !!}'>
        <td colspan=12 class=subjecttitle width=180>DEPORTMENT
        </td>
        <td colspan=10 class=deportment width=75>{{ $grades['deportment'][1] ?? '' }}</td>
        <td colspan=10 class=deportment width=75>{{ $grades['deportment'][2] ?? '' }}</td>
        <td colspan=6 class=attendance>{{ $grades['deportment'][5] ?? '' }}</td>
        <td colspan=4 class=grade></td>
        <td colspan=1 class=effort></td>
        <td colspan=7 class=remarks></td>
    </tr>
    <tr style='mso-height-source:userset;{!! $height !!}'>
        <td colspan=32 class=cardlabel width=480 style="text-align: right;">GENERAL AVERAGE
        </td>
        <td colspan=6 class=attendance>{{ $grades["generalaverage"] ?? '' }}</td>
        <td colspan=5 class=deportment>{{ $grades["totalunits"] ?? '' }}</td>
        <td colspan=7 class=remarks>{{ $grades["generalremarks"]  ?? '' }}</td>
    </tr>
@endsection
