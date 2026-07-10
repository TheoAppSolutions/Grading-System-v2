<?php

use App\Http\Controllers\Migration\UserMigrationController;
use App\Http\Controllers\Migration\GradeMigrationController;
use Illuminate\Support\Facades\Route;
use Barryvdh\Snappy\Facades\SnappyPdf;

Route::get('/', function () {
    return redirect(url('/app'));
});

// routes/web.php or api.php
Route::prefix('migrate')->group(function () {

    // Users
    Route::get('/users/preview',    [UserMigrationController::class, 'preview']);
    Route::get('/users/execute',    [UserMigrationController::class, 'migrate']);
    Route::get('/users/unfilled',   [UserMigrationController::class, 'unfilledEmails']);
    Route::get('/users/reset',      [UserMigrationController::class, 'reset']);

    Route::get('/grades/preview',     [GradeMigrationController::class, 'preview']);
    Route::get('/grades/status',      [GradeMigrationController::class, 'status']);
    Route::get('/grades/level/{num}', [GradeMigrationController::class, 'migrateTable']);
    Route::get('/grades/unfilled',    [GradeMigrationController::class, 'unfilled']);
    Route::get('/grades/reset',       [GradeMigrationController::class, 'reset']);
    Route::get('/grades/reset/{num}', [GradeMigrationController::class, 'resetTable']);
});

Route::get('/grades/pdf', function (\App\Models\Grade $record) {
    $pdf = SnappyPdf::loadView('reports.templates.upper_school.secondary_T1-T3', [
        'grades' => $record,
        'quarter' => 4,
        'student' => $record->load('student'),
    ])
    ->setOption('page-size', 'A4')
    ->setOption('orientation', 'Landscape')
    ->setOption('margin-top', 0)
    ->setOption('margin-bottom', 0)
    ->setOption('margin-left', 0)
    ->setOption('margin-right', 0);

    return $pdf->inline("Report-{$record->id}.pdf");
});