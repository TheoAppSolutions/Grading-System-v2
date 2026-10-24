<?php

use App\Http\Controllers\ReportController;
use App\Models\FileStorage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect(url('/app'));
});

Route::middleware('auth')->prefix('report')->group(function () {
    Route::get('/quarter/batch/{school_year_id}/{grade_level_id?}', [ReportController::class, 'generateQuarterBatch'])
        ->whereNumber('school_year_id')
        ->whereNumber('grade_level_id')
        ->name('report.quarter.batch');
    Route::get('/quarter/{school_year_id}/{student_id}', [ReportController::class, 'generateQuarter'])->name('report.quarter');
    Route::get('/award/{school_year_id}/{student_id}', [ReportController::class, 'generateAward'])->name('report.award');
    Route::get('/awards/student/{student_id}', [ReportController::class, 'generateStudentAwardSummary'])->name('report.award.student.summary');
    Route::get('/awards/summary/{school_year_id}/{grade_level_id}', [ReportController::class, 'generateAwardSummary'])->name('report.award.summary');
    Route::get('/tor/{student_id}', [ReportController::class, 'generateTOR'])->name('report.tor');
});

Route::get('/admin/file-storage/{fileStorage}/download', function (FileStorage $fileStorage) {
    abort_unless(
        filled($fileStorage->hashed_filename)
            && Storage::disk('public')->exists($fileStorage->hashed_filename),
        404,
    );

    return Storage::disk('public')->download(
        $fileStorage->hashed_filename,
        $fileStorage->file_name ?: basename($fileStorage->hashed_filename),
    );
})->middleware('auth')->name('admin.file-storage.download');
