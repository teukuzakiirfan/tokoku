<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\EnsureAktif;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/kasir');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth', EnsureAktif::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Kasir & admin
    Route::get('/kasir', [PosController::class, 'index'])->name('pos');
    Route::post('/kasir/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index'); // kasir: katalog & cek stok

    Route::get('/riwayat', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/riwayat/{transaksi}/struk', [TransaksiController::class, 'receipt'])->name('transaksi.receipt');
    Route::get('/riwayat/{transaksi}/struk.txt', [TransaksiController::class, 'receiptText'])->name('transaksi.text');

    // Khusus admin
    Route::middleware(AdminOnly::class)->group(function () {
        Route::resource('produk', ProdukController::class)->except(['index', 'show']);
        Route::post('/produk/{produk}/stok', [ProdukController::class, 'restock'])->name('produk.restock');

        Route::resource('pengguna', UserController::class)->names('users')->parameters(['pengguna' => 'user'])->except('show');

        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/unduh', [ReportController::class, 'csv'])->name('reports.csv');
    });
});
