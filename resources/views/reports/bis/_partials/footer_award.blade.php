<tr style='height:15.0pt'>
    <td colspan=20 height=20 class=report-card__comments-title style='height:15.0pt'>AWARDS EARNED:</td>
    <td colspan=37 class=report-card__comments-title>SUBJECT AREA:</td>
</tr>

@php($ctr=0)

<tr style='height:15.0pt'>
    <td colspan=8 height=20 class=report-card__checkmark style='height:15.0pt'>{!! $award['HIGHESTHONORS'] ?? '' !!}</td>
    <td colspan=9 class=report-card__comments>With Highest Honors</td>
    <td colspan=3 class=report-card__blank></td>

    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
</tr>
<tr style='height:15.0pt'>
    <td colspan=8 height=20 class=report-card__checkmark style='height:15.0pt'>{!! $award['HIGHHONORS'] ?? '' !!}</td>
    <td colspan=9 class=report-card__comments>With High Honors</td>
    <td colspan=3 class=report-card__blank></td>

    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
</tr>
<tr style='height:15.0pt'>
    <td colspan=8 height=20 class=report-card__checkmark style='height:15.0pt'>{!! $award['HONORS']  ?? '' !!}</td>
    <td colspan=9 class=report-card__comments>With Honors</td>
    <td colspan=3 class=report-card__blank></td>

    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
</tr>
<tr style='height:15.0pt'>
    <td colspan=8 height=20 class=report-card__checkmark style='height:15.0pt'>{!! $award["DEPORTMENT"] ?? '' !!}</td>
    <td colspan=9 class=report-card__comments>Deportment</td>
    <td colspan=3 class=report-card__blank></td>

    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
</tr>
<tr style='height:15.0pt'>
    <td colspan=8 height=20 class=report-card__checkmark style='height:15.0pt'>{!! $award['EFFORT'] ?? '' !!}</td>
    <td colspan=9 class=report-card__comments>Effort</td>
    <td colspan=3 class=report-card__blank></td>

    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
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
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>

    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
    @if(isset($subjects[$ctr]))
        @include('reports.templates._snippets.award_subject')
        @php($ctr++)
    @endif
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
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
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td colspan=17 height=20 class=report-card__comments-title style='height:15.0pt'>GUIDELINES / PROTOCOL
    </td>
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
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td colspan=24 height=20 class=report-card__comments style='height:15.0pt'>The Awards to Compute for the Recognition Ceremony
    </td>
    <td colspan=33 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__comments-title>A.&nbsp;</td>
    <td colspan=13 class=report-card__comments-title>Academic Excellence Award:</td>
    <td colspan=41 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td colspan=24 class=report-card__comments>With Highest Honors: 96 - 100%</td>
    <td colspan=28 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td colspan=24 class=report-card__comments>With High Honors: 93 - 95.99%</td>
    <td colspan=28 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td colspan=24 class=report-card__comments>With Honors: 90-92.99%</td>
    <td colspan=28 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__comments-title>B.&nbsp;</td>
    <td colspan=12 class=report-card__comments-title>Subject Excellence Award:</td>
    <td colspan=42 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td colspan=31 class=report-card__comments>Highest Cumulative Grade Average (per subject)<br>PE only: (1 male; 1 female)
    </td>
    <td colspan=21 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__comments-title>C.&nbsp;</td>
    <td colspan=11 class=report-card__comments-title>Effort Excellence Award:</td>
    <td colspan=43 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td colspan=47 class=report-card__comments>All 5's in final average of All Major Subjects term-summary__subjects
        (All Major Subjects academic term-summary__subjects and sub-average for the non-academic subjects).
    </td>
    <td colspan=5 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__comments-title>D.&nbsp;</td>
    <td colspan=14 class=report-card__comments-title>Deportment Excellence Award:</td>
    <td colspan=40 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td colspan=36 class=report-card__comments>Any student getting an average of 90% and above with out any report-card__grade below 85%.
    </td>
    <td colspan=16 class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
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
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
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
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td colspan=18 rowspan=2 class=report-card__student-info style='border-bottom:1.0pt solid black'>
        {{ $homeroom ?? '' }}
    </td>
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
    <td colspan=18 rowspan=2 class=report-card__student-info style='border-bottom:1.0pt solid black'>
        {{ $principal ?? '' }}
    </td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
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
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height:15.0pt'>
    <td height=20 class=report-card__blank style='height:15.0pt'></td>
    <td class=report-card__blank></td>
    <td colspan=18 class=report-card__office-label>Homeroom Teacher</td>
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
    <td colspan=18 class=report-card__office-label>Principal</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
