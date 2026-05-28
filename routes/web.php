<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Migration\StudentMigrationController;
use App\Http\Controllers\Migration\SchoolYearMigrationController;
use App\Http\Controllers\Migration\UserMigrationController;
use App\Http\Controllers\Migration\SubjectMigrationController;
use App\Http\Controllers\Migration\SubjectUserMigrationController;
use App\Http\Controllers\Migration\SubjectUnitMigrationController;
use App\Http\Controllers\Migration\SubjectExcludedMigrationController;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php or api.php
Route::prefix('migrate')->group(function () {

    // Students
    Route::get('/students/preview', [StudentMigrationController::class, 'preview']);
    Route::get('/students/execute', [StudentMigrationController::class, 'migrate']);
    Route::get('/students/reset',   [StudentMigrationController::class, 'reset']);

    // School Years
    Route::get('/school-years/preview', [SchoolYearMigrationController::class, 'preview']);
    Route::get('/school-years/execute', [SchoolYearMigrationController::class, 'migrate']);
    Route::get('/school-years/reset',   [SchoolYearMigrationController::class, 'reset']);

    Route::get('/users/preview',  [UserMigrationController::class, 'preview']);
    Route::get('/users/execute',  [UserMigrationController::class, 'migrate']);
    Route::get('/users/unfilled', [UserMigrationController::class, 'unfilledEmails']);
    Route::get('/users/reset',    [UserMigrationController::class, 'reset']);

    Route::get('/subjects/preview',  [SubjectMigrationController::class, 'preview']);
    Route::get('/subjects/execute',  [SubjectMigrationController::class, 'migrate']);
    Route::get('/subjects/unfilled', [SubjectMigrationController::class, 'unfilledGradeLevels']);
    Route::get('/subjects/reset',    [SubjectMigrationController::class, 'reset']);

    Route::get('/subject-users/preview',  [SubjectUserMigrationController::class, 'preview']);
    Route::get('/subject-users/execute',  [SubjectUserMigrationController::class, 'migrate']);
    Route::get('/subject-users/unfilled', [SubjectUserMigrationController::class, 'unfilledSchoolYears']);
    Route::get('/subject-users/reset',    [SubjectUserMigrationController::class, 'reset']);

    Route::get('/subject-units/preview',  [SubjectUnitMigrationController::class, 'preview']);
    Route::get('/subject-units/execute',  [SubjectUnitMigrationController::class, 'migrate']);
    Route::get('/subject-units/unfilled', [SubjectUnitMigrationController::class, 'unfilledSchoolYears']);
    Route::get('/subject-units/reset',    [SubjectUnitMigrationController::class, 'reset']);

    Route::get('/subject-excluded/preview',  [SubjectExcludedMigrationController::class, 'preview']);
    Route::get('/subject-excluded/execute',  [SubjectExcludedMigrationController::class, 'migrate']);
    Route::get('/subject-excluded/unfilled', [SubjectExcludedMigrationController::class, 'unfilledSchoolYears']);
    Route::get('/subject-excluded/reset',    [SubjectExcludedMigrationController::class, 'reset']);
});
