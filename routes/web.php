<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    
    Route::resource('orcamentos', OrcamentoController::class);

    Route::get('/orcamentos/{orcamento}/gastos/create', [GastoController::class, 'create'])->name('gastos.create');
    Route::post('/orcamentos/{orcamento}/gastos', [GastoController::class, 'store'])->name('gastos.store');
    Route::get('/gastos/{gasto}/edit', [GastoController::class, 'edit'])->name('gastos.edit');
    Route::put('/gastos/{gasto}', [GastoController::class, 'update'])->name('gastos.update');
    Route::delete('/gastos/{gasto}', [GastoController::class, 'destroy'])->name('gastos.destroy');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});

Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('orcamentos.index') 
        : redirect()->route('login');
});