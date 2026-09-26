<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\JasaController;
use App\Http\Controllers\Admin\SparepartController;
use App\Http\Controllers\Admin\LogistikController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\PayrollController;
use App\Http\Controllers\Owner\CashflowController;
use App\Http\Controllers\Owner\LabaRugiController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\AbsensiController;
use App\Http\Controllers\Karyawan\TugasController;
use App\Http\Controllers\Karyawan\ProfilController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Bengkel Yami - Workshop Management System Routes
|
*/

// ============================================================================
// Guest Routes
// ============================================================================

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================================
// Admin Routes (accessible by admin & owner)
// ============================================================================

Route::prefix('admin')
    ->middleware(['auth', 'role:admin,owner'])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Customers
        Route::resource('customers', CustomerController::class);

        // Karyawan & Absensi Manual Update
        Route::resource('karyawan', KaryawanController::class);
        Route::post('karyawan/{id}/absensi/update', [KaryawanController::class, 'updateAbsensi'])->name('karyawan.absensi.update');

        // Kategori Jasa
        Route::resource('kategori_jasa', \App\Http\Controllers\Admin\KategoriJasaController::class)->except(['show']);

        // Jasa
        Route::resource('jasa', JasaController::class);

        // Pengaturan Nota
        Route::get('pengaturan_nota', [\App\Http\Controllers\Admin\PengaturanNotaController::class, 'edit'])->name('pengaturan_nota.edit');
        Route::put('pengaturan_nota', [\App\Http\Controllers\Admin\PengaturanNotaController::class, 'update'])->name('pengaturan_nota.update');

        // Sparepart
        Route::resource('sparepart', SparepartController::class);
        Route::post('sparepart/{id}/restock', [SparepartController::class, 'restock'])->name('sparepart.restock');

        // Logistik
        Route::resource('logistik', LogistikController::class);
        Route::post('logistik/{id}/restock', [LogistikController::class, 'restock'])->name('logistik.restock');
        Route::post('logistik/{id}/decrease', [LogistikController::class, 'decrease'])->name('logistik.decrease');

        // Pesanan
        Route::resource('pesanan', PesananController::class);
        Route::get('pesanan/{id}/print', [PesananController::class, 'printEstimasi'])->name('pesanan.print');
        Route::put('pesanan/{id}/status', [PesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
        Route::put('pesanan/jasa-karyawan/{id}/status', [PesananController::class, 'updateJasaKaryawanStatus'])->name('pesanan.updateJasaKaryawanStatus');

        // Pembayaran / Transaksi
        Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('pembayaran/create/{pesanan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
        Route::post('pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::get('pembayaran/{id}', [PembayaranController::class, 'show'])->name('pembayaran.show');
        Route::get('pembayaran/{id}/print', [PembayaranController::class, 'printNota'])->name('pembayaran.print');
        Route::put('pembayaran/{id}/pelunasan', [PembayaranController::class, 'pelunasan'])->name('pembayaran.pelunasan');

        // Pengeluaran
        Route::resource('pengeluaran', PengeluaranController::class);
    });

// ============================================================================
// Owner Routes
// ============================================================================

Route::prefix('owner')
    ->middleware(['auth', 'role:owner'])
    ->name('owner.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        // Payroll
        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::get('payroll/{id}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::put('payroll/{id}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
        Route::put('payroll/{id}/unapprove', [PayrollController::class, 'unapprove'])->name('payroll.unapprove');
        Route::put('payroll/{id}/pay', [PayrollController::class, 'pay'])->name('payroll.pay');
        Route::put('payroll/{id}/unpay', [PayrollController::class, 'unpay'])->name('payroll.unpay');
        Route::put('payroll/{id}/recalculate', [PayrollController::class, 'recalculate'])->name('payroll.recalculate');
        Route::delete('payroll/{id}', [PayrollController::class, 'destroy'])->name('payroll.destroy');

        // Cashflow / Arus Kas
        Route::get('cashflow', [CashflowController::class, 'index'])->name('cashflow.index');

        // Laba Rugi
        Route::get('laba-rugi', [LabaRugiController::class, 'index'])->name('laba-rugi.index');
        Route::get('laporan/pendapatan-kategori', [\App\Http\Controllers\Owner\LaporanPendapatanController::class, 'index'])->name('laporan.pendapatan_kategori');

        // Manajemen Pengguna (Admin & Owner)
        Route::resource('users', \App\Http\Controllers\Owner\UserController::class)->except(['show', 'destroy']);
    });

// ============================================================================
// Karyawan Routes
// ============================================================================

Route::prefix('karyawan')
    ->middleware(['auth', 'role:karyawan'])
    ->name('karyawan.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');

        // Absensi
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('absensi/clock-in', [AbsensiController::class, 'clockIn'])->name('absensi.clockIn');
        Route::post('absensi/clock-out', [AbsensiController::class, 'clockOut'])->name('absensi.clockOut');

        // Tugas
        Route::get('tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::put('tugas/{id}/status', [TugasController::class, 'updateStatus'])->name('tugas.updateStatus');

        // Profil
        Route::get('profil', [ProfilController::class, 'index'])->name('profil.index');
        Route::put('profil/password', [ProfilController::class, 'updatePassword'])->name('profil.updatePassword');
    });
