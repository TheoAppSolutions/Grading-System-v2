@php($height = "height:18.0pt;")

@extends('reports.templates._layouts.T1-T3')

@section('content')
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
        <td colspan=2 class=midquarterreport-card__student-label>Grades</td>
        <td colspan=4 class=midquarterreport-card__student-label>Remarks</td>
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
        <td colspan=2 class=midquarterreport-card__student-label>Grades</td>
        <td colspan=4 class=midquarterreport-card__student-label>Remarks</td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=9 height=20 class=midquarter-report__subjects style='height:15.0pt'>LANGUAGE ARTS - GRAMMAR
        </td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['ENGLISH']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['ENGLISH'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__subjects>MATHEMATICS</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['MATH']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['MATH'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Uses correct sentence structure in
            oral/written form
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S1'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=17 class=midquarter-report__comments>Comprehends math concepts</td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['MATH']['S1'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Uses capital letters and punctuation
            marks
            correctly
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S2'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=17 class=midquarter-report__comments>Has mastered basic facts</td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['MATH']['S2'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Expresses ideas in paragraph form
            clearly
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S3'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=17 class=midquarter-report__comments>Applies math skills in solving word problems</td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['MATH']['S3'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Applies spelling skills effectively
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S4'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
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
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Edits written words
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S5'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__subjects>CHRISTIAN FORMATION</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['CF']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers>{{ $grades['CF'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__block></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Uses proper strokes in handwriting
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S6'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=17 class=midquarter-report__comments>Applies Biblical truths in daily living</td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['CF']['S1'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
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
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=9 height=20 class=midquarter-report__subjects style='height:15.0pt'>LANGUAGE ARTS -
            READING
        </td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['ENGLISH']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['ENGLISH'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__subjects>CIVICS and CULTURE</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['CIVICS']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['CIVICS'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Reads fluently
            with expression
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S7'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=17 class=midquarter-report__comments>Interprets symbols and reads the map/globe
            accurately
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['CIVICS']['S1'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Reads literature materials with meaning
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S8'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=17 class=midquarter-report__comments>Respects flexibility in adapting to cultural
            diversity in school
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['CIVICS']['S2'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Uses new vocabulary in oral and written
            work
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S9'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
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
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Responds to reading experiences</td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S10'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__subjects>MINOR SUBJECTS:</td>
        <td colspan=14 class=midquarter-report__block></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Uses comprehension strategies for
            reading and
            writing connections
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['ENGLISH']['S11'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__minor>LIVELIHOOD</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['LIVELIHOOD']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['LIVELIHOOD'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=report-card__blank style='height:15.0pt'>
        </td>
        <td colspan=2 class=report-card__blank></td>
        <td colspan=4 class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__minor>COMPUTER</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['COMPUTER']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['COMPUTER'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=report-card__blank style='height:15.0pt'>
        </td>
        <td colspan=2 class=report-card__blank></td>
        <td colspan=4 class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__minor>MUSIC</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['MUSIC']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['MUSIC'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
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
        <td colspan=9 class=midquarter-report__minor>ART</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['ART']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['ART'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=9 height=20 class=midquarter-report__subjects style='height:15.0pt'>FILIPINO</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['FILIPINO']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['FILIPINO'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=9 class=midquarter-report__minor>PE</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['PE']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers>{{ $grades['PE'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Comprehends
            basic concepts at report-card__grade level
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['FILIPINO']['S1'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Applies knowledge in writing skills
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['FILIPINO']['S2'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=3 class=report-card__blank>Legend:</td>
        <td colspan=6 class=report-card__blank><b>3</b> - Highly Proficient,</td>
        <td colspan=4 class=report-card__blank><b>2</b> - Proficient,</td>
        <td colspan=10 class=report-card__blank><b>1</b> - Progressing Towards Standard</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Can express ideas in oral form
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['FILIPINO']['S3'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
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
        <td class=report-card__school-year colspan="7" rowspan="3">
            {{-- {!! signatureTeacher($homeroom_id) !!} --}}
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
        <td class=report-card__school-year colspan="7" rowspan="3">
            {{-- {!! signatureMiss() !!} --}}
        </td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=9 height=20 class=midquarter-report__subjects style='height:15.0pt'>SCIENCE</td>
        <td colspan=8 class=midquarter-report__minor>{{ $grades['SCIENCE']['teacher'] ?? '' }}</td>
        <td colspan=2 class=midquarter-report__teachers
        >{{ $grades['SCIENCE'][$quarter]['midquarter'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__teachers></td>
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
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Makes
            inferences / formulates conclusions
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['SCIENCE']['S1'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__comments></td>
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
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Performs
            experiments independently
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['SCIENCE']['S2'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__comments></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=7 class=report-card__sidenote-title>{{ $homeroom ?? '' }}</td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=7 class=report-card__sidenote-title>{{ $principal ?? '' }}</td>
        <td class=report-card__blank></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Answers
            high-order thinking questions
        </td>
        <td colspan=2 class=midquarter-report__teachers>{{ $comments['SCIENCE']['S3'] ?? '' }}</td>
        <td colspan=4 class=midquarter-report__comments></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=7 class=term-summary__undertitle>Homeroom Teacher</td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td class=report-card__blank></td>
        <td colspan=7 class=term-summary__undertitle>School Principal</td>
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
    </tr>
    <tr style='height:15.0pt'>
        <td height=20 class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
        <td class=report-card__blank style="border-top: 2px solid black"></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__subjects style='height:15.0pt'>PERSONAL AND SOCIAL RELATIONSHIPS
        </td>
        <td colspan=3 class=midquarter-report__teachers>English</td>
        <td colspan=3 class=midquarter-report__teachers>Filipino</td>
        <td colspan=3 class=midquarter-report__teachers>Science</td>
        <td colspan=3 class=midquarter-report__teachers>Math</td>
        <td colspan=3 class=midquarter-report__teachers>CF</td>
        <td colspan=3 class=midquarter-report__teachers>Civics</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Is attentive</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['PSR1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['PSR1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['PSR1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['PSR1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['PSR1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['PSR1'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Cooperates willingly with teachers and
            peers
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['PSR2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['PSR2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['PSR2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['PSR2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['PSR2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['PSR2'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Shows respect for authority
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['PSR3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['PSR3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['PSR3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['PSR3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['PSR3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['PSR3'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Respects rights and property of others
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['PSR4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['PSR4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['PSR4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['PSR4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['PSR4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['PSR4'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Exercises self-control and
            self-discipline
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['PSR5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['PSR5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['PSR5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['PSR5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['PSR5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['PSR5'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__subjects style='height:15.0pt'>WORK AND STUDY SKILLS
        </td>
        <td colspan=18 class=midquarter-report__teachers></td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Follows directions
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['WSS1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['WSS1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['WSS1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['WSS1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['WSS1'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['WSS1'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Completes work on time
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['WSS2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['WSS2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['WSS2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['WSS2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['WSS2'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['WSS2'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Works independently
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['WSS3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['WSS3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['WSS3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['WSS3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['WSS3'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['WSS3'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Does his / her best in assigned tasks
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['WSS4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['WSS4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['WSS4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['WSS4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['WSS4'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['WSS4'] ?? '' }}</td>
    </tr>
    <tr style='height:15.0pt'>
        <td colspan=17 height=20 class=midquarter-report__comments style='height:15.0pt'>Contributes to class discussions
        </td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['ENGLISH']['WSS5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['FILIPINO']['WSS5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['SCIENCE']['WSS5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['MATH']['WSS5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CF']['WSS5'] ?? '' }}</td>
        <td colspan=3 class=midquarter-report__teachers>{{ $comments['CIVICS']['WSS5'] ?? '' }}</td>
    </tr>
@endsection
