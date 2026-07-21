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
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }}
                class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>{{ $grades['LANGUAGE'][$ctr]['final'] ?? '' }}</td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final><span class="report-card__effort">{{ $grades['LANGUAGE'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['LANGUAGE']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- LANGUAGE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;READING</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['READING'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['READING'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['READING']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- READING --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;FILIPINO</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['FILIPINO'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['FILIPINO'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['FILIPINO']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- FILIPINO --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;  width:160pt'>&nbsp;SCIENCE</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['SCIENCE'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['SCIENCE'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['SCIENCE']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- SCIENCE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;MATHEMATICS</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['MATH'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['MATH'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['MATH']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- MATHEMATICS --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;CHRISTIAN FORMATION</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['CF'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['CF'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['CF']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank></td>
    </tr>{{-- CHRISTIAN FORMATION --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt; width:160pt'>&nbsp;CIVICS AND CULTURE</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['CIVICS'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['CIVICS'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['CIVICS']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- CIVICS AND CULTURE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;<b>MAPEH</b></td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['MAPEH'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['MAPEH'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks></td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- MAPE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Music</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['MUSIC'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['MUSIC'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['MUSIC']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- MUSIC --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Art</td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['ART'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['ART'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['ART']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- ARTS --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Physical Education & Health
        </td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['PE'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['PE'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['PE']['report-card__remarks'] ?? '' }}</td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- PHYSICAL EDUCATION --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;<b>TLE</b>
        </td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['TLE'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['TLE'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks></td>
        <td colspan=1 class=report-card__blank>&nbsp;</td>
    </tr>{{-- TLE --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;HELE
        </td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['LIVELIHOOD'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['LIVELIHOOD'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
        <td colspan=7 class=report-card__remarks>{{ $grades['LIVELIHOOD']['report-card__remarks'] ?? '' }}</td>
    </tr>{{-- LIVELIHOOD EDUCATION --}}
    <tr style='height:18.75pt'>
        <td colspan=16 height=25 class=term-summary__subjects width=210 style='height:18.75pt;
  width:160pt'>&nbsp;&nbsp;&nbsp;&nbsp;Computer Education
        </td>
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 3: 4 }} class=term-summary__grade--final {{ $ctr < 5 ? "width=40" : "" }}>
                {{ $grades['COMPUTER'][$ctr]['final'] ?? '' }}
            </td>
            <td colspan={{ $ctr < 5 ? 1: 2 }} class=term-summary__effort--final>
                <span class="report-card__effort">{{ $grades['COMPUTER'][$ctr]['report-card__effort'] ?? '' }}</span>
            </td>
        @endfor
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
        @for($ctr = 1; $ctr <= 5; $ctr++)
            <td colspan={{ $ctr < 5 ? 4: 6 }} class=report-card__deportment {{ $ctr < 5 ? "width=40": "" }} style='width:30pt'>{{ $grades['report-card__deportment'][$ctr] ?? '' }}</td>
        @endfor
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
