@php($height = "height:17.0pt;")

@extends('reports.hcgs._layouts.T2-T4')

@section('content')
    @foreach($subjects as $subject)
        @if(strpos($subject['subject_title'], 'Elective') === false)
            <tr style='mso-height-source:userset;{!! $height !!}'>
                <td colspan=12 class=subjecttitle
                    width=180>{{ $subject['subject_title'] }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][1]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][1]['effort'] ?? '' }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][2]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][2]['effort'] ?? '' }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][3]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][3]['effort'] ?? '' }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][4]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][4]['effort'] ?? '' }}</td>
                <td colspan=4 class=grade>{{ $grades[$subject['subject_title']][5]['rawfinal'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][5]['effort'] ?? '' }}</td>
                <td colspan=5 class=grade
                    style="text-align: center !important;">{{ $grades[$subject['subject_title']]['unit'] ?? '' }}</td>
                <td colspan=7 class=remarks>{{ $grades[$subject['subject_title']]['remarks'] ?? '' }}</td>
                <td class=blank></td>
            </tr>
        @endif
    @endforeach

    {{-- @foreach($subjects as $subject)
        @if(strpos($subject['subject_category'], 'Elective') !== false)
            <tr style='mso-height-source:userset;{!! $height !!}'>
                <td colspan=12 class=subjecttitle
                    width=180>{{ $subject['subject_category'] }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][1]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][1]['effort'] ?? '' }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][2]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][2]['effort'] ?? '' }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][3]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][3]['effort'] ?? '' }}</td>
                <td colspan=3 class=grade width=45>{{ $grades[$subject['subject_title']][4]['final'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][4]['effort'] ?? '' }}</td>
                <td colspan=4 class=grade>{{ $grades[$subject['subject_title']][5]['rawfinal'] ?? '' }}</td>
                <td colspan=2 class=effort>{{ $grades[$subject['subject_title']][5]['effort'] ?? '' }}</td>
                <td colspan=5 class=grade
                    style="text-align: center !important;">{{ $grades[$subject['subject_title']]['unit'] ?? '' }}</td>
                <td colspan=7 class=remarks>{{ $grades[$subject['subject_title']]['remarks'] ?? '' }}</td>
                <td class=blank></td>
            </tr>
        @endif
    @endforeach --}}

    <tr style='mso-height-source:userset;{!! $height !!}'>
        <td colspan=12 class=subjecttitle width=180>DEPORTMENT
        </td>
        <td colspan=5 class=deportment width=75>{{ $grades['deportment'][1] ?? '' }}</td>
        <td colspan=5 class=deportment width=75>{{ $grades['deportment'][2] ?? '' }}</td>
        <td colspan=5 class=deportment width=75>{{ $grades['deportment'][3] ?? '' }}</td>
        <td colspan=5 class=deportment width=75>{{ $grades['deportment'][4] ?? '' }}</td>
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
