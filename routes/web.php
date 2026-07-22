<?php

use App\Http\Controllers\Migration\UserMigrationController;
use App\Http\Controllers\Migration\GradeMigrationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(url('/app'));
});

Route::prefix('migrate')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/preview',    [UserMigrationController::class, 'preview'])->name('users.preview');
        Route::get('/execute',    [UserMigrationController::class, 'migrate'])->name('users.migrate');
        Route::get('/unfilled',   [UserMigrationController::class, 'unfilledEmails'])->name('users.unfilledEmails');
        Route::get('/reset',      [UserMigrationController::class, 'reset'])->name('users.reset');
    });
});

Route::prefix('report')->group(function () {
    Route::get('/quarter/{school_year_id}/{student_id}',    [ReportController::class, 'generateQuarter'])->name('report.quarter');
    Route::get('/midQuarter/{school_year_id}/{student_id}', [ReportController::class, 'generateMidQuarter'])->name('report.midQuarter');
    Route::get('/award/{school_year_id}/{student_id}',      [ReportController::class, 'generateAward'])->name('report.award');
    Route::get('/subject/{school_year_id}/{student_id}',    [ReportController::class, 'generateSubject'])->name('report.subject');
    Route::get('/tor/{student_id}',                         [ReportController::class, 'generateTOR'])->name('report.tor');
});
