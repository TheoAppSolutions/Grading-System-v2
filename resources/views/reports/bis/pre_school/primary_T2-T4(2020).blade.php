@php($height = "height:18.0pt;")

@extends('reports.templates._layouts.T2-T4')

@section('content')
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>CRE</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['CRE'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['CRE'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['CRE'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['CRE'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['CRE'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['CRE'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['CRE'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['CRE'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['CRE'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['CRE'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['CRE']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['CRE']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>A</td>
        <td colspan=6 class=report-card__sidenote>93 - 96.99</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>C</td>
        <td colspan=5 class=report-card__sidenote>73 - 76.99</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>English</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ENGLISH'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ENGLISH'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ENGLISH'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ENGLISH'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ENGLISH'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ENGLISH'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ENGLISH'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ENGLISH'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['ENGLISH'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ENGLISH'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['ENGLISH']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['ENGLISH']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>A-</td>
        <td colspan=6 class=report-card__sidenote>90 - 92.99</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>C-</td>
        <td colspan=5 class=report-card__sidenote>70 - 72.99</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>HASS</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HASS'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HASS'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HASS'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HASS'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HASS'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HASS'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HASS'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HASS'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['HASS'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HASS'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['HASS']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['HASS']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>B+</td>
        <td colspan=6 class=report-card__sidenote>87 - 89.99</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>D+</td>
        <td colspan=5 class=report-card__sidenote>67 - 69.99</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>Health</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HEALTH'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HEALTH'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HEALTH'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HEALTH'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HEALTH'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HEALTH'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['HEALTH'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HEALTH'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['HEALTH'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['HEALTH'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['HEALTH']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['HEALTH']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>B</td>
        <td colspan=6 class=report-card__sidenote>83 - 86.99</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>D</td>
        <td colspan=5 class=report-card__sidenote>63 - 66.99</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>ICT/Practical Skills</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ICT/PRACTICAL SKILLS'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ICT/PRACTICAL SKILLS'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ICT/PRACTICAL SKILLS'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ICT/PRACTICAL SKILLS'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ICT/PRACTICAL SKILLS'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ICT/PRACTICAL SKILLS'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ICT/PRACTICAL SKILLS'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ICT/PRACTICAL SKILLS'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['ICT/PRACTICAL SKILLS'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ICT/PRACTICAL SKILLS'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['ICT/PRACTICAL SKILLS']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['ICT/PRACTICAL SKILLS']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>B-</td>
        <td colspan=6 class=report-card__sidenote>80 - 82.99</td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>D-</td>
        <td colspan=5 class=report-card__sidenote>60 - 62.99</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>Making a Living</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MAKING A LIVING'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MAKING A LIVING'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MAKING A LIVING'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MAKING A LIVING'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MAKING A LIVING'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MAKING A LIVING'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MAKING A LIVING'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MAKING A LIVING'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['MAKING A LIVING'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MAKING A LIVING'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['MAKING A LIVING']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['MAKING A LIVING']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=2 class=report-card__blank>F</td>
        <td colspan=5 class=report-card__sidenote>Below 60</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>Mathematics</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MATHEMATICS'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MATHEMATICS'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MATHEMATICS'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MATHEMATICS'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MATHEMATICS'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MATHEMATICS'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['MATHEMATICS'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MATHEMATICS'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['MATHEMATICS'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['MATHEMATICS'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['MATHEMATICS']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['MATHEMATICS']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>P.E.
        </td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['P.E.'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['P.E.'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['P.E.'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['P.E.'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['P.E.'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['P.E.'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['P.E.'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['P.E.'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['P.E.'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['P.E.'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['P.E.']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['P.E.']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=16 class=report-card__sidenote-title>EFFORT GRADE</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>Science
        </td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['SCIENCE'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['SCIENCE'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['SCIENCE'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['SCIENCE'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['SCIENCE'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['SCIENCE'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['SCIENCE'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['SCIENCE'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['SCIENCE'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['SCIENCE'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['SCIENCE']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['SCIENCE']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=16 class=report-card__sidenote>5 - Exerting maximum effort</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>The Arts
        </td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ARTS'][1]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ARTS'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ARTS'][2]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ARTS'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ARTS'][3]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ARTS'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 class=report-card__grade width=45>{{ $grades['ARTS'][4]['final'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ARTS'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['ARTS'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 class=report-card__effort>{{ $grades['ARTS'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 class=report-card__grade>{{ $grades['ARTS']['unit'] ?? '' }}</td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['ARTS']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=16 class=report-card__sidenote>4 - Exerting above satisfactory effort</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 rowspan=2 class=report-card__subject-title width=180>Elective (Music Private Lessons)
        </td>
        <td colspan=3 rowspan=2 class=report-card__grade width=45>{{ $grades['MUSIC'][1]['final'] ?? '' }}</td>
        <td colspan=2 rowspan=2 class=report-card__effort>{{ $grades['MUSIC'][1]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 rowspan=2 class=report-card__grade width=45>{{ $grades['MUSIC'][2]['final'] ?? '' }}</td>
        <td colspan=2 rowspan=2 class=report-card__effort>{{ $grades['MUSIC'][2]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 rowspan=2 class=report-card__grade width=45>{{ $grades['MUSIC'][3]['final'] ?? '' }}</td>
        <td colspan=2 rowspan=2 class=report-card__effort>{{ $grades['MUSIC'][3]['report-card__effort'] ?? '' }}</td>
        <td colspan=3 rowspan=2 class=report-card__grade width=45>{{ $grades['MUSIC'][4]['final'] ?? '' }}</td>
        <td colspan=2 rowspan=2 class=report-card__effort>{{ $grades['MUSIC'][4]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 rowspan=2 class=report-card__grade>{{ $grades['MUSIC'][5]['rawfinal'] ?? '' }}</td>
        <td colspan=2 rowspan=2 class=report-card__effort>{{ $grades['MUSIC'][5]['report-card__effort'] ?? '' }}</td>
        <td colspan=4 rowspan=2 class=report-card__grade>{{ $grades['MUSIC']['unit'] ?? '' }}</td>
        <td colspan=1 rowspan=2 class=report-card__effort></td>
        <td colspan=7 rowspan=2 rowspan=2 class=report-card__remarks>{{ $grades['MUSIC']['report-card__remarks'] ?? '' }}</td>
        <td class=report-card__blank></td>
        <td colspan=16 class=report-card__sidenote>3 - Exerting satisfactory effort</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td class=report-card__blank></td>
        <td colspan=16 class=report-card__sidenote>2 - Exerting below satisfactory effort</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=12 class=report-card__subject-title width=180>DEPORTMENT</td>
        <td colspan=5 class=report-card__deportment width=75>{{ $grades['report-card__deportment'][1] ?? '' }}</td>
        <td colspan=5 class=report-card__deportment width=75>{{ $grades['report-card__deportment'][2] ?? '' }}</td>
        <td colspan=5 class=report-card__deportment width=75>{{ $grades['report-card__deportment'][3] ?? '' }}</td>
        <td colspan=5 class=report-card__deportment width=75>{{ $grades['report-card__deportment'][4] ?? '' }}</td>
        <td colspan=6 class=report-card__attendance>{{ $grades['report-card__deportment'][5] ?? '' }}</td>
        <td colspan=5 class=report-card__grade></td>
        <td colspan=7 class=report-card__remarks></td>
        <td class=report-card__blank></td>
        <td colspan=16 class=report-card__sidenote>1 - Exerting minimum effort</td>
    </tr>
    <tr style='{!! $height !!}'>
        <td colspan=32 class=report-card__card-label width=480 style="text-align: right;">GENERAL AVERAGE
        </td>
        <td colspan=6 class=report-card__attendance>{{ $grades["term-summary__average"] ?? '' }}</td>
        <td colspan=5 class=report-card__deportment>{{ $grades["totalunits"] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks>{{ $grades["generalremarks"]  ?? '' }}</td>

        <td class=report-card__blank></td>
    </tr>
@endsection
