<?php

use App\Models\FileStorage;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(url('/app'));
});

Route::middleware('auth')->prefix('report')->group(function () {
    Route::get('/quarter/{school_year_id}/{student_id}', [ReportController::class, 'generateQuarter'])->name('report.quarter');
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
