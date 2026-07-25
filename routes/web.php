<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\HrdController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/file-storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    abort_unless(file_exists($fullPath), 404);

    return response()->file($fullPath);
})->where('path', '.*')->name('file.storage');

// ── Auth ──────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Employee ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/leaves', [EmployeeController::class, 'index'])->name('index');
    Route::get('/leaves/create', [EmployeeController::class, 'create'])->name('create');
    Route::post('/leaves', [EmployeeController::class, 'store'])->name('store');
    Route::get('/leaves/{leaveApplication}', [EmployeeController::class, 'show'])->name('show');
});

// ── Manager ───────────────────────────────────────────────────────
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    // Employee leave management
    Route::get('/employee-leaves', [ManagerController::class, 'employeeLeaves'])->name('employee-leaves');
    Route::get('/employee-leaves/{leaveApplication}', [ManagerController::class, 'showEmployeeLeave'])->name('show-employee-leave');
    Route::post('/employee-leaves/{leaveApplication}/action', [ManagerController::class, 'approveLeave'])->name('approve-leave');
    // Manager's own leave
    Route::get('/my-leaves', [ManagerController::class, 'myLeaves'])->name('my-leaves');
    Route::get('/my-leaves/create', [ManagerController::class, 'createLeave'])->name('create-leave');
    Route::post('/my-leaves', [ManagerController::class, 'storeLeave'])->name('store-leave');
});

// ── HRD ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:hrd'])->prefix('hrd')->name('hrd.')->group(function () {
    Route::get('/dashboard', [HrdController::class, 'dashboard'])->name('dashboard');
    Route::get('/all-leaves', [HrdController::class, 'allLeaves'])->name('all-leaves');
    Route::get('/all-leaves/{leaveApplication}', [HrdController::class, 'showLeave'])->name('show-leave');
    Route::post('/all-leaves/{leaveApplication}/action', [HrdController::class, 'approveLeave'])->name('approve-leave');
    Route::get('/report', [HrdController::class, 'reportForm'])->name('report');
    Route::post('/report/generate', [HrdController::class, 'generateReport'])->name('report.generate');
});

// ── Admin ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('create-user');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('update-user');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('toggle-user');
});
