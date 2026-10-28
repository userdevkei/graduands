<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Student\VerificationController;
use Illuminate\Support\Facades\Route;

/* ---------------- Student-facing ---------------- */
Route::get('/', [VerificationController::class, 'index'])->name('student.lookup');
Route::post('/lookup', [VerificationController::class, 'lookup'])->middleware('throttle:10,1')->name('student.lookup.submit');

Route::middleware('student.session')->group(function () {
    Route::get('/my-record', [VerificationController::class, 'show'])->name('student.details');
    Route::put('/my-record', [VerificationController::class, 'update'])->name('student.update');
    Route::post('/exit', [VerificationController::class, 'exit'])->name('student.exit');
});

/* ---------------- Admin ---------------- */
Route::get('/admin/login', [Admin\AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [Admin\AuthController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.submit');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('cohorts', Admin\CohortController::class)->except('show');

    Route::get('graduands/export', [Admin\GraduandController::class, 'export'])->name('graduands.export');
    Route::get('graduands', [Admin\GraduandController::class, 'index'])->name('graduands.index');
    Route::get('graduands/{graduand}', [Admin\GraduandController::class, 'show'])->name('graduands.show');
    Route::get('graduands/{graduand}/edit', [Admin\GraduandController::class, 'edit'])->name('graduands.edit');
    Route::put('graduands/{graduand}', [Admin\GraduandController::class, 'update'])->name('graduands.update');
    Route::post('graduands/{graduand}/unlock', [Admin\GraduandController::class, 'unlock'])->name('graduands.unlock');

    Route::get('imports', [Admin\ImportController::class, 'create'])->name('imports.create');
    Route::get('imports/template', [Admin\ImportController::class, 'template'])->name('imports.template');
    Route::post('imports', [Admin\ImportController::class, 'store'])->name('imports.store');
    Route::get('imports/{batch}', [Admin\ImportController::class, 'preview'])->name('imports.preview');
    Route::post('imports/{batch}/commit', [Admin\ImportController::class, 'commit'])->name('imports.commit');
    Route::delete('imports/{batch}', [Admin\ImportController::class, 'destroy'])->name('imports.destroy');

    Route::get('logs', [Admin\LogController::class, 'index'])->name('logs.index');
});
