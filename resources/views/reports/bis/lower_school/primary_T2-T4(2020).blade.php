@php($height = "height:18.0pt;")

@extends('reports.templates._layouts.T2-T4')

@section('content')
    <tr style='height:18.75pt'>
        <td colspan=16 rowspan=2 height=50 class=report-card__card-label width=210
            style='height:37.5pt;width:160pt'>SUBJECTS
        </td>
        <td colspan=16 class=report-card__card-label>QUARTERLY</td>
        <td colspan=6 rowspan=2 class=report-card__card-label width=70 style='width:52pt'>FINAL<br>
            GRADE
        </td>
        <td colspan=7 rowspan=2 class=report-card__card-label>REMARKS</td>
        <td colspan=1 class=report-card__blank></td>
        <td colspan=12 rowspan="14">
            @include('reports.templates._snippets.card_score_board')
        </td>
    </tr>{{-- TABLE HEADERS --}}
    <tr style='height:18.75pt'>
        <td colspan=4 height=25 class=term-summary__quarter style='height:18.75pt;'>1</td>
        <td colspan=4 class=term-summary__quarter>2</td>
        <td colspan=4 class=term-summary__quarter>3</td>
        <td colspan=4 class=term-summary__quarter>4</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- QUARTER HEADERS --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;LANGUAGE
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['LANGUAGE'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['LANGUAGE'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['LANGUAGE'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['LANGUAGE'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['LANGUAGE'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['LANGUAGE'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['LANGUAGE'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['LANGUAGE'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['LANGUAGE'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['LANGUAGE']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- LANGUAGE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;READING</td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['READING'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['READING'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['READING'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['READING'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['READING'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['READING'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['READING'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['READING'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['READING'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['READING']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- READING --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;FILIPINO</td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['FILIPINO'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['FILIPINO'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['FILIPINO'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['FILIPINO'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['FILIPINO'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['FILIPINO'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['FILIPINO'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['FILIPINO'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['FILIPINO'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['FILIPINO']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- FILIPINO --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;  width:160pt'>&nbsp;SCIENCE</td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['SCIENCE'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['SCIENCE'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['SCIENCE'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['SCIENCE'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['SCIENCE'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['SCIENCE'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['SCIENCE'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['SCIENCE'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['SCIENCE'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['SCIENCE']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- SCIENCE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;MATHEMATICS</td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MATH'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MATH'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MATH'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MATH'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MATH'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MATH'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['MATH'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['MATH'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['MATH'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['MATH']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- MATHEMATICS --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;CHRISTIAN FORMATION
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['CF'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['CF'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['CF'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['CF'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['CF'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['CF'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['CF'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['CF'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['CF'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['CF']['report-card__remarks'] ?? '' }}</td>
    </tr>{{-- CHRISTIAN FORMATION --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;CIVICS AND CULTURE
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['CIVICS'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['CIVICS'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['CIVICS'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['CIVICS'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['CIVICS'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['CIVICS'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['CIVICS'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['CIVICS'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['CIVICS'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['CIVICS']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- CIVICS AND CULTURE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;<b>MAPEH</b>
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MAPEH'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MAPEH'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MAPEH'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MAPEH'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MAPEH'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MAPEH'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final width=40>{{ $grades['MAPEH'][4]['final'] ?? '' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['MAPEH'][5]['final'] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks></td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- MAPE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Music
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MUSIC'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MUSIC'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MUSIC'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MUSIC'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['MUSIC'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['MUSIC'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['MUSIC'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['MUSIC'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['MUSIC'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['MUSIC']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- MUSIC --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Art
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['ART'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['ART'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['ART'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['ART'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['ART'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['ART'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['ART'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['ART'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['ART'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['ART']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- ARTS --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Physical Education & Health
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['PE'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['PE'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['PE'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['PE'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['PE'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['PE'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['PE'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['PE'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['PE'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['PE']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- PHYSICAL EDUCATION --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;<b>TLE</b>
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['TLE'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['TLE'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['TLE'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['TLE'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['TLE'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['TLE'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final width=40>{{ $grades['TLE'][4]['final'] ?? '' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['TLE'][5]['final'] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks></td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- TLE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Livelihood Education
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['LIVELIHOOD'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['LIVELIHOOD'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['LIVELIHOOD'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['LIVELIHOOD'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['LIVELIHOOD'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['LIVELIHOOD'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;" width=40>
            @if($student->gradelevel >= 4 and $student->gradelevel <= 6)
                {{ $grades['LIVELIHOOD'][4]['report-card__remarks'] ?? 'P' }}
            @endif
        </td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['LIVELIHOOD'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['LIVELIHOOD'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['LIVELIHOOD']['report-card__remarks'] ?? '' }}</td>
    </tr>{{-- LIVELIHOOD EDUCATION --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Computer Education
        </td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['COMPUTER'][1]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['COMPUTER'][1]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['COMPUTER'][2]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['COMPUTER'][2]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=3 class=term-summary__grade--final width=40>{{ $grades['COMPUTER'][3]['final'] ?? '' }}</td>
        <td colspan=1 class=term-summary__effort--final><span class="report-card__effort">{{ $grades['COMPUTER'][3]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=4 class=term-summary__grade--final style="font-size: 13px; text-align: center; font-style: italic;"
            width=40>{{ $grades['COMPUTER'][4]['report-card__remarks'] ?? 'P' }}</td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['COMPUTER'][5]['final'] ?? '' }}<span class="report-card__effort">{{ $grades['COMPUTER'][5]['report-card__effort'] ?? '' }}</span></td>
        <td colspan=7 class=report-card__remarks>{{ $grades['COMPUTER']['report-card__remarks'] ?? '' }}</td>
    </tr>{{-- COMPUTER EDUCATION --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;ELECTIVE
        </td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=4 class=term-summary__grade--final></td>
        <td colspan=2 class=term-summary__effort--final></td>
        <td colspan=7 class=report-card__remarks>&nbsp;</td>
    </tr>{{-- ELECTIVE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;DEPORTMENT
        </td>
        <td colspan=4 class=report-card__deportment width=40 style='width:30pt'>{{ $grades['report-card__deportment'][1] ?? '' }}</td>
        <td colspan=4 class=report-card__deportment width=40 style='width:30pt'>{{ $grades['report-card__deportment'][2] ?? '' }}</td>
        <td colspan=4 class=report-card__deportment width=40 style='width:30pt'>{{ $grades['report-card__deportment'][3] ?? '' }}</td>
        <td colspan=4 class=report-card__deportment width=40 style='width:30pt'>{{ $grades['report-card__deportment'][4] ?? '' }}</td>
        <td colspan=6 class=report-card__deportment>{{ $grades['report-card__deportment'][5] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks>{{ $grades['report-card__deportment']['report-card__remarks'] ?? '' }}</td>
    </tr>{{-- DEPORTMENT --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;CLUB
        </td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=3 class=term-summary__grade--final width=40 style='width:30pt'></td>
        <td colspan=1 class=term-summary__effort--final></td>
        <td colspan=3 class=term-summary__grade--final></td>
        <td colspan=3 class=term-summary__effort--final></td>
        <td colspan=7 class=report-card__remarks>&nbsp;</td>
    </tr>{{-- CLUB --}}
    <tr style='height:18.75pt'>
        <td colspan=32 height=25 class=term-summary__average width=430 style='height:18.75pt;
  width:324pt'>GENERAL AVERAGE&nbsp;
        </td>
        <td colspan=6 class=term-summary__grade--final style="text-align: center;">{{ $grades['term-summary__average'] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks>{{ $grades['generalremarks'] ?? '' }}</td>
    </tr>{{-- GENERAL AVERAGE --}}
@endsection
