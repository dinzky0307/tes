<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/upload', [EmployeeController::class, 'upload'])->name('employees.upload');
Route::get('/employees/live-search', [EmployeeController::class, 'liveSearch'])->name('employees.liveSearch');


Route::post('/employees/{employee}/validation', [EmployeeController::class, 'updateValidation'])
    ->name('employees.updateValidation');
