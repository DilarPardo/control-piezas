<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ProyectosController;
use App\Http\Controllers\BloquesController;
use App\Http\Controllers\RegistrosController;
use App\Http\Controllers\PiezasController;


// Redirigir raíz al login directamente
Route::get('/', function () {
    return redirect('/login');
});

// Dashboard (solo para usuarios autenticados)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// Rutas de autenticación (login, register, etc.)
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/proyectos', [ProyectosController::class, 'index'])->name('proyectos.index');
    Route::post('/proyectos', [ProyectosController::class, 'store'])->name('proyectos.store');
    Route::put('/proyectos/{proyecto}', [ProyectosController::class, 'update'])->name('proyectos.update');
    Route::delete('/proyectos/{proyecto}', [ProyectosController::class, 'destroy'])->name('proyectos.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/bloques', [BloquesController::class, 'index'])->name('bloques.index');
    Route::post('/bloques', [BloquesController::class, 'store'])->name('bloques.store');
    Route::put('/bloques/{bloque}', [BloquesController::class, 'update'])->name('bloques.update');
    Route::delete('/bloques/{bloque}', [BloquesController::class, 'destroy'])->name('bloques.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/registros', [RegistrosController::class, 'index'])->name('registros.index');
    Route::post('/registros', [RegistrosController::class, 'store'])->name('registros.store');
    Route::put('/registros/{registro}', [RegistrosController::class, 'update'])->name('registros.update');
    Route::delete('/registros/{registro}', [RegistrosController::class, 'destroy'])->name('registros.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/piezas', [PiezasController::class, 'index'])->name('piezas.index');
    Route::post('/piezas', [PiezasController::class, 'store'])->name('piezas.store');
    Route::put('/piezas/{pieza}', [PiezasController::class, 'update'])->name('piezas.update');
    Route::delete('/piezas/{pieza}', [PiezasController::class, 'destroy'])->name('piezas.destroy');
});
