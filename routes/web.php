<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\AnalyticController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReviewController;
use App\Models\Book;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Hapus: use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ===============================================
// GUEST & CATALOG ROUTE
// ===============================================
Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{book}', [CatalogController::class, 'show'])->name('catalog.show');


// ===============================================
// ROUTE MEMBUTUHKAN AUTENTIKASI
// ===============================================
Route::middleware('auth')->group(function () {
    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.delete');

    // DASHBOARD MAHASISWA (Menggunakan Namespace Penuh)
    Route::get('/mahasiswa/dashboard', [App\Http\Controllers\DashboardController::class, 'showMahasiswaDashboard'])
        ->name('mahasiswa.dashboard');

    // LOAN ACTIONS (Mahasiswa)
    Route::post('/loan/{book}/borrow', [LoanController::class, 'borrow'])->name('loan.borrow');
    Route::patch('/loan/{loan}/renew', [LoanController::class, 'renew'])->name('loan.renew');

    // BOOK REVIEWS
    Route::post('/reviews/{book}', [ReviewController::class, 'store'])->name('reviews.store');

    // RESERVASI BUKU
    Route::post('/reservation/{book}', [LoanController::class, 'reserve'])->name('reservation.reserve');
    Route::delete('/reservation/{loan}', [LoanController::class, 'cancelReservation'])->name('reservation.cancel');
});


// ===============================================
// ROUTE BOOK MANAGEMENT (Admin & Pegawai)
// ===============================================
Route::middleware(['auth', 'role:admin,pegawai'])->prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index');
    Route::get('create', [BookController::class, 'create'])->name('create');
    Route::post('/', [BookController::class, 'store'])->name('store');
    Route::get('{book}', [BookController::class, 'show'])->name('show');
    Route::get('{book}/edit', [BookController::class, 'edit'])->name('edit');
    Route::patch('{book}', [BookController::class, 'update'])->name('update');
    Route::delete('{book}', [BookController::class, 'destroy'])->name('destroy');
});


// ===============================================
// ROUTE PEGAWAI
// ===============================================
Route::middleware(['auth', 'role:pegawai,admin'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', function () {
        return view('pegawai.dashboard');
    })->name('dashboard');

    // LOAN ACTIONS (Pegawai)
    Route::get('loans/pending', [LoanController::class, 'pendingLoans'])->name('loans.pending');
    Route::post('loans/{loan}/return', [LoanController::class, 'processReturn'])->name('loans.return');
    Route::patch('fines/{fine}/pay', [LoanController::class, 'processFinePayment'])->name('fines.pay');

    // Daftar Reservasi Pegawai
    Route::get('reservations', [LoanController::class, 'pendingReservations'])->name('reservations.pending');
    Route::post('reservations/{loan}/activate', [LoanController::class, 'activateReservation'])->name('reservations.activate');
});


// ===============================================
// ROUTE UNTUK ADMIN
// ===============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // USER MANAGEMENT
    Route::resource('users', UserController::class);

    // ANALYTICS & REPORTING
    Route::get('/analytics', [AnalyticController::class, 'index'])->name('analytics.index');
});


require __DIR__.'/auth.php';
