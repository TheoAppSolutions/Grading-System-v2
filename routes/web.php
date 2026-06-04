<?php

use App\Http\Controllers\Migration\UserMigrationController;
use App\Http\Controllers\Migration\GradeMigrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
