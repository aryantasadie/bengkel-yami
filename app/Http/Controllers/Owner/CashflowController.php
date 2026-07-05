<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashflowController extends Controller
{
    /**
     * Tampilkan laporan arus kas.
     */
    public function index(Request $request)
    {
        // Default filter: bulan ini
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->endOfMonth()->toDateString());
        $kategori = $request->input('kategori');

        // Pemasukan: dari transaksi (total_bayar)
        $queryPemasukan = Transaksi::whereDate('tanggal', '>=', $tanggalMulai)
            ->whereDate('tanggal', '<=', $tanggalAkhir);

        $pemasukans = $queryPemasukan->with('pesanan.customer')
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Pemasukan riil (Cash Basis) = total_bayar - sisa_bayar
        $totalPemasukan = $queryPemasukan->sum(\Illuminate\Support\Facades\DB::raw('total_bayar - sisa_bayar'));

        // Pengeluaran: dari pengeluaran (nominal)
        $queryPengeluaran = Pengeluaran::whereDate('tanggal', '>=', $tanggalMulai)
            ->whereDate('tanggal', '<=', $tanggalAkhir);

        if ($kategori) {
            $queryPengeluaran->where('kategori', $kategori);
        }

        $pengeluarans = $queryPengeluaran->orderBy('tanggal', 'desc')->get();
        $totalPengeluaran = $pengeluarans->sum('nominal');

        // Saldo
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Daftar kategori pengeluaran
        $kategoris = [
            'operasional',
            'gaji',
            'restock_sparepart',
            'restock_logistik',
            'lainnya',
        ];

        return view('owner.cashflow.index', compact(
            'pemasukans',
            'pengeluarans',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'tanggalMulai',
            'tanggalAkhir',
            'kategori',
            'kategoris'
        ));
    }
}
