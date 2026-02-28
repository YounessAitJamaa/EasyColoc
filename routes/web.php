<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\ColocationAdminController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Colocation routes
    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('/colocations/{id}', [ColocationController::class, 'show'])->name('colocations.show');

    // Invitation routes
    Route::get('/colocations/{id}/invite', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/colocations/{id}/invite', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/invitations/accept/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/refuse/{token}', [InvitationController::class, 'refuse'])->name('invitations.refuse');

    // Category routes
    Route::get('/colocations/{colocationId}/categories', [CategorieController::class, 'index'])->name('categories.index');
    Route::post('/colocations/{colocationId}/categories', [CategorieController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{id}', [CategorieController::class, 'destroy'])->name('categories.destroy');

    // Expense routes
    Route::get('/colocations/{colocationId}/depenses', [DepenseController::class, 'index'])->name('depenses.index');
    Route::post('/colocations/{colocationId}/depenses', [DepenseController::class, 'store'])->name('depenses.store');
    Route::delete('/depenses/{id}', [DepenseController::class, 'destroy'])->name('depenses.destroy');

    // Admin routes
    Route::get('/colocations/{id}/admin', [ColocationAdminController::class, 'index'])->name('colocations.admin');
    Route::patch('/colocations/{id}/admin', [ColocationAdminController::class, 'update'])->name('colocations.admin.update');
    Route::delete('/colocations/{id}/admin', [ColocationAdminController::class, 'destroy'])->name('colocations.admin.destroy');

    // Paiement routes
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');

    // --- ADMINISTRATION GLOBALE ---
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    });
});

require __DIR__ . '/auth.php';