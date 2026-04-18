<?php

use App\Http\Controllers\Api\ApiPersonController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.auth')->group(function () {
    Route::get('/persons', [ApiPersonController::class, 'index']);
});

// Catch all invalid API routes
Route::fallback(function () {
    return response()->json([
        'success'    => false,
        'error_code' => 'ROUTE_001',
        'message'    => 'API endpoint not found.',
    ], 404);
});