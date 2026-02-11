<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\AuthController;

// --- ROUTE TAMU ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk mendapatkan notifikasi admin
Route::get('/activations/get-notif', [ActivationController::class, 'getAdminNotifications']);

// --- ROUTE USER LOGIN ---
Route::middleware(['auth'])->group(function () {

    // 1. Redirect Halaman Utama (Root) langsung ke Activations
    Route::get('/', function () {
        return redirect()->route('activations.index');
    });

    Route::post('/activations/start-engine', [ActivationController::class, 'startEngine'])->name('activations.start');
    Route::post('/activations/stop-engine', [ActivationController::class, 'stopEngine'])->name('activations.stop');

    // 2. Resource Activations
    Route::resource('activations', ActivationController::class);

    // 3. Generate Token
    Route::post('/activations/generate/{id}', [ActivationController::class, 'generate'])->name('activations.generate');

    Route::get('/create', [ActivationController::class, 'create'])->name('activations.create');

    // 4. Delete Logger
    Route::delete('/activations/{id}', [ActivationController::class, 'destroy'])->name('activations.destroy');

});