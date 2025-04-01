<?php

use App\Http\Controllers\NcmCodeController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Documentation
Route::get('/swagger/openapi.json', function () {
    $path = storage_path('api-docs/openapi.json');
    if (!file_exists($path)) {
        abort(404, 'Arquivo openapi.json não encontrado.');
    }
    return response()->file($path);
});

Route::get('/ncm/docs', function () {
    return view('swagger-ui.index');
});

// Recursos
Route::get('/ncm', [NcmCodeController::class, 'index'])->name('ncm.index');
Route::get('/ncm/search', [NcmCodeController::class, 'search'])->name('ncm.search');
Route::get('/ncm/categories', [NcmCodeController::class, 'categories'])->name('ncm.categories');
Route::post('/ncm/import', [NcmCodeController::class, 'import'])->name('ncm.import');
Route::get('/ncm/{code}', [NcmCodeController::class, 'show'])->name('ncm.show');
Route::get('/ncm/{code}/subcategories', [NcmCodeController::class, 'subcategories'])->name('ncm.subcategories');
Route::get('/ncm/{code}/history', [NcmCodeController::class, 'history'])->name('ncm.history');
Route::get('/ncm/search/advanced', [NcmCodeController::class, 'advancedSearch'])->name('ncm.advancedSearch');
Route::get('/ncm/reports/totals', [ReportController::class, 'totals'])->name('ncm.reports.totals');
Route::get('/ncm/reports/valid', [ReportController::class, 'valid'])->name('ncm.reports.valid');
