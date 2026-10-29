<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LoanController;

Route::get('/', fn()=> return redirect()->route('equipments.index'));

Route::resource('equipments', EquipmentController::class)->only(['index', 'create', 'store', 'show']);
Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');