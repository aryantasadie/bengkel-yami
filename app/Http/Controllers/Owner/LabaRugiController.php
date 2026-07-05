<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\PesananJasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LabaRugiController extends Controller
{
    /**
     * Tampilkan laporan laba rugi.
     */
    public function index(Request $request)
    {
        // Default: bulan dan tahun saat ini
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Pendapatan: sum transaksi total_bayar HANYA JIKA LUNAS
        $pendapatan = Transaksi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_bayar', 'lunas')
            ->sum('total_bayar');

        // HPP: sum harga_beli sparepart yang digunakan HANYA DARI TRANSAKSI LUNAS
        $hpp = DB::table('pesanan_sparepart')
            ->join('pesanan', 'pesanan_sparepart.pesanan_id', '=', 'pesanan.id')
            ->join('transaksi', 'transaksi.pesanan_id', '=', 'pesanan.id')
            ->whereMonth('transaksi.tanggal', $bulan)
            ->whereYear('transaksi.tanggal', $tahun)
            ->where('transaksi.status_bayar', 'lunas')
            ->sum(DB::raw('pesanan_sparepart.harga_beli_snapshot * pesanan_sparepart.qty'));

        // Beban Operasional: sum pengeluaran (operasional, gaji, restock_logistik)
        // restock_logistik dianggap beban langsung habis pakai.
        $bebanOperasional = Pengeluaran::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereIn('kategori', ['operasional', 'gaji', 'restock_logistik'])
            ->sum('nominal');

        // Beban Lainnya (kategori: lainnya)
        // PENTING: 'restock_sparepart' TIDAK DIHITUNG di Laba Rugi karena sudah masuk perhitungan HPP (Harga Pokok Penjualan) saat barang terjual.
        // Jika dihitung lagi, akan terjadi double-deduction (pengurangan ganda).
        $bebanLainnya = Pengeluaran::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('kategori', 'lainnya')
            ->sum('nominal');

        // Perhitungan
        $labaKotor = $pendapatan - $hpp;
        $labaBersih = $labaKotor - $bebanOperasional - $bebanLainnya;

        // Daftar bulan untuk filter
        $daftarBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $daftarBulan[$i] = Carbon::create(null, $i, 1)->translatedFormat('F');
        }

        return view('owner.laba-rugi.index', compact(
            'pendapatan',
            'hpp',
            'bebanOperasional',
            'bebanLainnya',
            'labaKotor',
            'labaBersih',
            'bulan',
            'tahun',
            'daftarBulan'
        ));
    }
}
