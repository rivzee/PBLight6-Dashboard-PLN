<?php

use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index']);      // GET all
    Route::get('/{id}', [UserApiController::class, 'show']);   // GET by ID
    Route::post('/', [UserApiController::class, 'store']);     // POST 1
    Route::put('/{id}', [UserApiController::class, 'update']); // UPDATE
    Route::delete('/{id}', [UserApiController::class, 'destroy']); // DELETE
});
Route::get('/tes-api', function () {
    return response()->json(['status' => 'API OK']);
});
