<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;

Route::prefix('persons')->group(function () {
    Route::get('/',              [PersonController::class, 'index']);
    Route::post('/',             [PersonController::class, 'store']);
    Route::post('/{id}',         [PersonController::class, 'update']);
    Route::delete('/{id}',       [PersonController::class, 'destroy']);
    Route::post('/bulk-delete',  [PersonController::class, 'bulkDestroy']);
});

Route::get('/{any}', fn() => view('app'))->where('any', '.*');
