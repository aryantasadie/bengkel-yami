<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard owner.
     */
    public function index()
    {
        $now = Carbon::now();

        // Total pendapatan bulan ini
        $pendapatanBulanIni = Transaksi::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->sum('total_bayar');

        // Total pengeluaran bulan ini
        $pengeluaranBulanIni = Pengeluaran::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->sum('nominal');

        // Saldo
        $saldoBulanIni = $pendapatanBulanIni - $pengeluaranBulanIni;

        // Total karyawan aktif
        $totalKaryawan = Karyawan::where('is_active', true)->count();

        // Chart data: pemasukan vs pengeluaran 6 bulan terakhir
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $bulan = $month->format('Y-m');
            $labelBulan = $month->translatedFormat('M Y');

            $pemasukan = Transaksi::whereMonth('tanggal', $month->month)
                ->whereYear('tanggal', $month->year)
                ->sum('total_bayar');

            $pengeluaran = Pengeluaran::whereMonth('tanggal', $month->month)
                ->whereYear('tanggal', $month->year)
                ->sum('nominal');

            $chartData[] = [
                'bulan'       => $labelBulan,
                'pemasukan'   => (float) $pemasukan,
                'pengeluaran' => (float) $pengeluaran,
            ];
        }

        return view('owner.dashboard', compact(
            'pendapatanBulanIni',
            'pengeluaranBulanIni',
            'saldoBulanIni',
            'totalKaryawan',
            'chartData'
        ));
    }
}
