<?php

use App\Http\Controllers\Migration\UserMigrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php or api.php
Route::prefix('migrate')->group(function () {

    // Users
    Route::get('/users/preview', [UserMigrationController::class, 'preview']);
    Route::get('/users/execute', [UserMigrationController::class, 'migrate']);
    Route::get('/users/unfilled', [UserMigrationController::class, 'unfilledEmails']);
    Route::get('/users/reset', [UserMigrationController::class, 'reset']);
});
