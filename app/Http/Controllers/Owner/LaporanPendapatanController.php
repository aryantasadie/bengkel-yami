<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KategoriJasa;

class LaporanPendapatanController extends Controller {
    public function index(Request $request) {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Ambil data pendapatan jasa per kategori berdasarkan transaksi yang selesai/lunas
        // Subtotal Jasa = pesanan_jasa.subtotal - (pesanan_jasa.subtotal * pesanan.diskon_persen / 100)
        
        $pendapatanKategori = DB::table('pesanan_jasa')
            ->join('jasa', 'pesanan_jasa.jasa_id', '=', 'jasa.id')
            ->leftJoin('kategori_jasa', 'jasa.kategori_jasa_id', '=', 'kategori_jasa.id')
            ->join('pesanan', 'pesanan_jasa.pesanan_id', '=', 'pesanan.id')
            ->join('transaksi', 'pesanan.id', '=', 'transaksi.pesanan_id')
            ->whereMonth('transaksi.tanggal', $bulan)
            ->whereYear('transaksi.tanggal', $tahun)
            ->whereIn('transaksi.status_bayar', ['lunas', 'dp']) // Asumsi dp juga masuk pendapatan, atau lunas saja?
            ->selectRaw('
                kategori_jasa.nama_kategori,
                SUM(pesanan_jasa.subtotal) as total_kotor,
                SUM(pesanan_jasa.subtotal * (1 - COALESCE(pesanan.diskon_persen, 0)/100)) as total_bersih
            ')
            ->groupBy('kategori_jasa.id', 'kategori_jasa.nama_kategori')
            ->get();

        return view('owner.laporan.pendapatan_kategori', compact('pendapatanKategori', 'bulan', 'tahun'));
    }
}
