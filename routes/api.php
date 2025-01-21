<?php

use App\Http\Controllers\NcmCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ncm', [NcmCodeController::class, 'index'])->name('ncm.index');
Route::get('/ncm/search', [NcmCodeController::class, 'search'])->name('ncm.search');
Route::get('/ncm/categories', [NcmCodeController::class, 'categories'])->name('ncm.categories');
Route::post('/ncm/import', [NcmCodeController::class, 'import'])->name('ncm.import');
Route::get('/ncm/{code}', [NcmCodeController::class, 'show'])->name('ncm.show');
Route::get('/ncm/{code}/subcategories', [NcmCodeController::class, 'subcategories'])->name('ncm.subcategories');
Route::get('/ncm/{code}/history', [NcmCodeController::class, 'history'])->name('ncm.history');
