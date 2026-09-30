<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CriterionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AhpController;
use App\Http\Controllers\SmartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DocumentationController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Criteria & Parameters
Route::prefix('kriteria')->name('criteria.')->group(function () {
    Route::get('/', [CriterionController::class, 'index'])->name('index');
    Route::post('/store', [CriterionController::class, 'store'])->name('store');
    Route::put('/{criterion}', [CriterionController::class, 'update'])->name('update');
    Route::delete('/{criterion}', [CriterionController::class, 'destroy'])->name('destroy');
    Route::post('/{criterion}/parameter', [CriterionController::class, 'storeParameter'])->name('parameter.store');
    Route::delete('/parameter/{parameter}', [CriterionController::class, 'destroyParameter'])->name('parameter.destroy');
});

// Students / Alternatives
Route::prefix('siswa')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index');
    Route::post('/store', [StudentController::class, 'store'])->name('store');
    Route::put('/{student}', [StudentController::class, 'update'])->name('update');
    Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
});

// Assessments
Route::prefix('penilaian')->name('assessments.')->group(function () {
    Route::get('/', [AssessmentController::class, 'index'])->name('index');
    Route::post('/store', [AssessmentController::class, 'store'])->name('store');
    Route::post('/batch', [AssessmentController::class, 'updateBatch'])->name('batch');
    Route::post('/reset-jurnal', [AssessmentController::class, 'resetToJournal'])->name('reset-journal');
});

// AHP Pairwise Comparisons
Route::prefix('ahp')->name('ahp.')->group(function () {
    Route::get('/', [AhpController::class, 'index'])->name('index');
    Route::post('/calculate', [AhpController::class, 'calculate'])->name('calculate');
    Route::post('/preset', [AhpController::class, 'loadPreset'])->name('preset');
    Route::post('/activate/{session}', [AhpController::class, 'activate'])->name('activate');
});

// SMART Ranking & Sensitivity Analysis
Route::prefix('perangkingan')->name('smart.')->group(function () {
    Route::get('/', [SmartController::class, 'index'])->name('index');
    Route::post('/simpan', [SmartController::class, 'saveResults'])->name('save');
});

// Printable Official Reports
Route::get('/laporan/cetak', [ReportController::class, 'print'])->name('reports.print');

// In-App Architecture & Model Documentation
Route::get('/dokumentasi', [DocumentationController::class, 'index'])->name('documentation.index');
