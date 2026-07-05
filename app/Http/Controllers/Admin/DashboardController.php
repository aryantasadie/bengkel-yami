<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin.
     */
    public function index()
    {
        $now = Carbon::now();

        // Total pesanan aktif (status: antrian atau proses)
        $pesananAktif = Pesanan::whereIn('status', ['antrian', 'proses'])->count();

        // Total pesanan selesai
        $pesananSelesai = Pesanan::where('status', 'selesai')->count();

        // Total pendapatan bulan ini (hanya untuk owner, admin tidak perlu)
        $pendapatanBulanIni = 0;
        if (auth()->user()->role === 'owner') {
            $pendapatanBulanIni = Transaksi::whereMonth('tanggal', $now->month)
                ->whereYear('tanggal', $now->year)
                ->sum('total_bayar');
        }

        // Total karyawan aktif
        $karyawanAktif = Karyawan::where('is_active', true)->count();

        // 5 pesanan terbaru
        $pesananTerbaru = Pesanan::with(['customer'])
            ->latest('tanggal_masuk')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'pesananAktif',
            'pesananSelesai',
            'pendapatanBulanIni',
            'karyawanAktif',
            'pesananTerbaru'
        ));
    }
}
