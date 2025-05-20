<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunController;
use Illuminate\Support\Facades\Auth;

// Test route untuk middleware role
Route::middleware(['auth'])->group(function () {
    // Test route untuk asisten_manager
    Route::get('/test-role', function() {
        $user = Auth::user();
        $output = "Middleware role berhasil dijalankan!<br>";
        $output .= "User saat ini: " . $user->name . "<br>";
        $output .= "Role: " . $user->role . "<br>";
        $output .= "Timestamp: " . now() . "<br>";

        return $output;
    })->middleware('role:asisten_manager');

    // Akses langsung ke controller
    Route::get('/test-akun', [AkunController::class, 'index']);
});
