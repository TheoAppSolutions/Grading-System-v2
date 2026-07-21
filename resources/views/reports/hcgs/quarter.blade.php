@extends('reports.templates.layouts.quarter')

@section('header-section')
    <tr style='height:17pt'>
        <td colspan=62 class=report-card__header width=930>HIGHLANDS CHRISTIAN GRAMMAR SCHOOL</td>
    </tr>
    <tr style='height:17pt'>
        <td colspan=62 class=report-card__subheader width=930>Mt. Hagen, WHP</td>
    </tr>
    <tr style='height:17pt'>
        <td colspan=62 class=report-card__subheader>Papua New Guinea</td>
    </tr>
@endsection

@section('header-semester')
    <td colspan=6 class=report-card__student-label>Semester:</td>
    <td colspan=2 class=report-card__student-info>{{ $quarter ?? 'N/A' }}</td>
@endsection

@section('content')
    <?php
        $subjectRows = [
            ['title' => 'Agriculture', 'key' => 'AGRICULTURE'],
            ['title' => 'The Arts', 'key' => 'ARTS'],
            ['title' => 'Business Studies', 'key' => 'BUSINESS STUDIES'],
            ['title' => 'Civics/Geography', 'key' => 'CIVICS'],
            ['title' => 'CRE', 'key' => 'CRE'],
            ['title' => 'English', 'key' => 'ENGLISH'],
            ['title' => 'Personal Development (Health)', 'key' => 'HEALTH'],
            ['title' => 'History', 'key' => 'HISTORY'],
            ['title' => 'ICT/Practical Skills', 'key' => 'ICT/PRACTICAL SKILLS'],
            ['title' => 'Making A Living/Home Economics', 'key' => 'LIVING/HOME ECONOMICS'],
            ['title' => 'Mathematics', 'key' => 'MATHEMATICS'],
            ['title' => 'Physical Education', 'key' => 'P.E.'],
            ['title' => 'Science', 'key' => 'SCIENCE'],
            ['title' => 'Social Science', 'key' => 'SOCIAL SCIENCE'],
            ['title' => 'Elective (Music Private Lessons)', 'key' => 'MUSIC'],
        ];
    ?>

    @foreach ($subjectRows as $row)
        @include('reports.templates.parts.subject_row', [
            'title' => $row['title'],
            'subject' => $subjects[$row['key']] ?? '',
            'grades' => $grades,
        ])
    @endforeach

    <tr style='height: 17pt'>
        <td colspan=12 class=report-card__subject-title width=180>DEPORTMENT
        </td>
        <td colspan=10 class=report-card__deportment width=75>{{ data_get($grades, 'deportment.1') }}</td>
        <td colspan=10 class=report-card__deportment width=75>{{ data_get($grades, 'deportment.2') }}</td>
        <td colspan=6 class=report-card__deportment>{{ data_get($grades, 'deportment.5') }}</td>
        <td colspan=4 class=report-card__grade></td>
        <td colspan=1 class=report-card__effort></td>
        <td colspan=7 class=report-card__remarks></td>
    </tr>
    <tr style='height: 17pt'>
        <td colspan=32 class=report-card__card-label width=480 style="text-align: right;">GENERAL AVERAGE
        </td>
        <td colspan=6 class=report-card__attendance>{{ $grades['term-summary__average'] ?? '' }}</td>
        <td colspan=5 class=report-card__deportment>{{ $grades['totalunits'] ?? '' }}</td>
        <td colspan=7 class=report-card__remarks>{{ $grades['generalremarks'] ?? '' }}</td>
    </tr>

    @include('reports.templates.parts.blank_row')

    @include('reports.templates.parts.comments_section', [
        'height' => '17pt',
        'selectedComments' => $selectedComments ?? [],
        'homeroomAdviser' => $homeroomAdviser ?? '',
        'schoolPrincipal' => $schoolPrincipal ?? '',
        'eligibleFor' => $eligibleFor ?? '',
    ])
@endsection

@section('attendance-section')
   
@endsection