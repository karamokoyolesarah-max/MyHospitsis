<?php

use App\Http\Controllers\LabRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active_user', 'role:lab_technician'])->group(function () {
    Route::get('/lab/dashboard', [LabRequestController::class, 'index'])->name('lab.dashboard');
    Route::get('/lab/worklist', [LabRequestController::class, 'worklist'])->name('lab.worklist');
    Route::get('/lab/history', [LabRequestController::class, 'history'])->name('lab.history');
    Route::get('/lab/inventory', [LabRequestController::class, 'inventory'])->name('lab.inventory.index');
    
    // Actions
    Route::post('/lab/requests/{lab_request}/status', [LabRequestController::class, 'updateStatus'])->name('lab.requests.status');
    Route::post('/lab/requests/{lab_request}/result', [LabRequestController::class, 'submitResult'])->name('lab.requests.result');
});

Route::middleware(['auth', 'active_user', 'role:doctor_lab'])->group(function () {
    Route::get('/lab/biologist/dashboard', [LabRequestController::class, 'biologistDashboard'])->name('lab.biologist.dashboard');
    Route::get('/lab/biologist/validation', [LabRequestController::class, 'validationList'])->name('lab.biologist.validation');
    Route::get('/lab/biologist/stats', [LabRequestController::class, 'biologistStats'])->name('lab.biologist.stats');
    Route::post('/lab/requests/{lab_request}/validate', [LabRequestController::class, 'validateResult'])->name('lab.requests.validate');
    Route::post('/lab/requests/{lab_request}/update-result', [LabRequestController::class, 'updateResult'])->name('lab.requests.update_result');
});

Route::middleware(['auth', 'active_user', 'role:doctor_radio'])->group(function () {
    Route::get('/lab/radiologist/dashboard', [\App\Http\Controllers\RadiologyController::class, 'dashboard'])->name('lab.radiologist.dashboard');
    Route::get('/lab/radiologist/validation', [\App\Http\Controllers\RadiologyController::class, 'validationList'])->name('lab.radiologist.validation');
    Route::get('/lab/radiologist/stats', [\App\Http\Controllers\RadiologyController::class, 'stats'])->name('lab.radiologist.stats');
    Route::post('/lab/radiology/{lab_request}/validate', [\App\Http\Controllers\RadiologyController::class, 'validateResult'])->name('lab.radiology.validate');
    Route::post('/lab/radiology/{lab_request}/update-result', [\App\Http\Controllers\RadiologyController::class, 'updateResult'])->name('lab.radiology.update_result');
});

Route::middleware(['auth', 'active_user', 'role:radio_technician'])->group(function () {
    Route::get('/lab/radio_technician/dashboard', [\App\Http\Controllers\RadiologyController::class, 'technicianDashboard'])->name('lab.radio_technician.dashboard');
    Route::get('/lab/radio_technician/worklist', [\App\Http\Controllers\RadiologyController::class, 'technicianWorklist'])->name('lab.radio_technician.worklist');
    Route::get('/lab/radio_technician/history', [\App\Http\Controllers\RadiologyController::class, 'technicianHistory'])->name('lab.radio_technician.history');
    Route::get('/lab/radio_technician/inventory', [\App\Http\Controllers\RadiologyController::class, 'technicianInventory'])->name('lab.radio_technician.inventory');
    
    // Inventory Actions (Using LabInventoryController since it's generic)
    Route::post('/lab/radio_technician/inventory', [\App\Http\Controllers\LabInventoryController::class, 'store'])->name('lab.radio_technician.inventory.store');
    Route::put('/lab/radio_technician/inventory/{labInventory}', [\App\Http\Controllers\LabInventoryController::class, 'update'])->name('lab.radio_technician.inventory.update');
    Route::delete('/lab/radio_technician/inventory/{labInventory}', [\App\Http\Controllers\LabInventoryController::class, 'destroy'])->name('lab.radio_technician.inventory.destroy');

    // Actions
    Route::post('/lab/radio_technician/requests/{id}/status', [\App\Http\Controllers\RadiologyController::class, 'updateStatus'])->name('lab.radio_technician.status');
    Route::post('/lab/radio_technician/requests/{id}/result', [\App\Http\Controllers\RadiologyController::class, 'storeResult'])->name('lab.radio_technician.result');
});
