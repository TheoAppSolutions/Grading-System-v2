@extends('reports.templates._layouts.T2-T4')

@section('content')
    <tr style='height:18.75pt'>
        <td colspan=20 rowspan=2 height=50 class=report-card__card-label width=210
            style='height:37.5pt;width:160pt'>SUBJECTS
        </td>
        <td colspan=10 class=report-card__card-label>QUARTER</td>
        <td colspan=6 rowspan=2 class=report-card__card-label width=70 style='width:52pt'>FINAL<br>GRADE
        </td>
        <td colspan=7 rowspan=2 class=report-card__card-label>REMARKS</td>
        <td class=report-card__blank></td>
        <td colspan=12 rowspan="14">
            @include('reports.templates._snippets.card_score_board')
        </td>
    </tr>{{-- TABLE HEADERS --}}
    <tr style='height:19pt;'>
        <td colspan=5 height=26 class=term-summary__quarter style='height:19pt;'>Midterm</td>
        <td colspan=5 class=term-summary__quarter style='border-left:none'>Finals</td>
        <td class=report-card__blank></td>
    </tr>

    @php($category = ['core', 'applied', 'specialized'])

    @foreach($category as $cat)
        @if(isset(${$cat}) and count(${$cat}) > 0)
            <tr class=report-card__blank style='height:19pt'>
                <td colspan=43 height=20 class=term-summary__subjects style='height:19pt;font-weight: bold;padding-left: 5px;'>
                    {{ ucfirst($cat) }} Subjects
                </td>
                <td class=report-card__blank></td>
            </tr>

            @for($ctr = 0; $ctr<count(${$cat}); $ctr++)
                @if(${$cat}[$ctr]->user_id != "")
                    <tr style='height:16.5pt'>
                        <td colspan=20 height=20 class=term-summary__subjects width=225
                            style='height:19pt;width:171pt;padding-left: 10px;'>
                            {{ ${$cat}[$ctr]->subjecttitle }}</td>
                        <td colspan=3 class=finalGrade>
                            {{ $grades[${$cat}[$ctr]->subjectname][$quarters[1]]['final'] ?? '' }}</td>
                        <td colspan=2 class=term-summary__effort--final><sub class="report-card__effort">
                                {{ $grades[${$cat}[$ctr]->subjectname][$quarters[1]]['report-card__effort'] ?? '' }}</sub>
                        </td>
                        <td colspan=3 class=finalGrade>
                            {{ $grades[${$cat}[$ctr]->subjectname][$quarters[2]]['final'] ?? '' }}</td>
                        <td colspan=2 class=term-summary__effort--final><sub class="report-card__effort">
                                {{ $grades[${$cat}[$ctr]->subjectname][$quarters[2]]['report-card__effort'] ?? '' }}</sub>
                        </td>
                        <td colspan=4 class=finalGrade>
                            {{ $grades[${$cat}[$ctr]->subjectname][5]['final'] ?? '' }}</td>
                        <td colspan=2 class=term-summary__effort--final><sub class="report-card__effort">
                                {{ $grades[${$cat}[$ctr]->subjectname][5]['report-card__effort'] ?? '' }}</sub></td>
                        <td colspan=7 class=report-card__remarks>
                            {{ $grades[${$cat}[$ctr]->subjectname]['report-card__remarks'] ?? '' }}</td>
                    </tr>
                @endif
            @endfor
        @endif
    @endforeach

    <tr style='height:19pt'>
        <td colspan=20 height=20 class=term-summary__subjects width=225 style='height:19pt;
  width:171pt'>&nbsp;DEPORTMENT
        </td>
        <td colspan=5 class=report-card__deportment
            style='width: 30px;'>{{ $grades['report-card__deportment'][$quarters[1]] ?? '' }}</td>
        <td colspan=5 class=report-card__deportment
            style='width: 30px;'>{{ $grades['report-card__deportment'][$quarters[2]] ?? '' }}</td>
        <td colspan=6 class=report-card__deportment style='width: 30px;'>{{ $grades['report-card__deportment'][5] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks>{{ $grades['report-card__deportment']['report-card__remarks'] ?? '' }}</td>
    </tr>
    <tr style='height:19pt'>
        <td colspan=30 height=20 class=term-summary__average style='height:19pt'>GENERAL AVERAGE</td>
        <td colspan=6 class=finalGrade style="text-align: center;">{{ $grades['term-summary__average'] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks>{{ $grades['generalremarks'] ?? '' }}</td>
    </tr>
@endsection
