<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// AUTH ROUTES
Route::post('/register', [AuthenticatedSessionController::class, 'register']);
Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login');
Route::get('/unauthorized', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Unauthorized'
    ], 401);
})->name('unauthorized');
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'logout']);

// USER MANAGEMENT ROUTES (hanya untuk admin)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);   // List Users
    Route::get('/users/{id}', [UserController::class, 'show']);   // Get Single User
    Route::post('/users', [UserController::class, 'store']);   // Create User
    Route::put('/users/{id}', [UserController::class, 'update']);   // Update User
    Route::delete('/users/{id}', [UserController::class, 'destroy']);   // Delete User
});


use App\Http\Controllers\TicketController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index']); // Lihat semua tiket
    Route::post('/tickets', [TicketController::class, 'store']); // Buat tiket baru
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']); // Lihat detail tiket
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment']); // Tambah komentar

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::put('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']); // Update status tiket
        Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']); // Hapus tiket
    });
});

