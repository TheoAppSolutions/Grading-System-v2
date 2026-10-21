<?php

use App\Http\Controllers\Admin\UpdaterController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(url('/app'));
});

Route::middleware('auth')->prefix('report')->group(function () {
    Route::get('/quarter/{school_year_id}/{student_id}', [ReportController::class, 'generateQuarter'])->name('report.quarter');
    Route::get('/tor/{student_id}', [ReportController::class, 'generateTOR'])->name('report.tor');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/updater', [UpdaterController::class, 'index'])->name('updater');
    Route::post('/updater/pull', [UpdaterController::class, 'pull'])->name('updater.pull');
});
