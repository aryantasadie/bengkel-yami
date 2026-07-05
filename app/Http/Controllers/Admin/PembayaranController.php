<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pesanan;
use App\Models\PesananJasa;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    /**
     * Tampilkan daftar transaksi/pembayaran.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['pesanan.customer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_nota', 'like', "%{$search}%")
                  ->orWhereHas('pesanan', function ($q2) use ($search) {
                      $q2->where('no_pesanan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pesanan.customer', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $transaksis = $query->latest('tanggal')->paginate(15)->withQueryString();

        return view('admin.pembayaran.index', compact('transaksis'));
    }

    /**
     * Tampilkan form pembayaran untuk pesanan yang selesai.
     */
    public function create($pesanan_id)
    {
        $pesanan = Pesanan::with(['customer', 'pesananJasa.jasa', 'pesananSparepart.sparepart'])
            ->findOrFail($pesanan_id);

        // Cek apakah sudah ada transaksi untuk pesanan ini
        if ($pesanan->transaksi) {
            return redirect()->route('admin.pembayaran.show', $pesanan->transaksi->id)
                ->with('info', 'Pesanan ini sudah memiliki nota pembayaran.');
        }

        // Hitung total dari pesanan_jasa dan pesanan_sparepart subtotals
        $totalHarga = $pesanan->pesananJasa->sum('subtotal') + $pesanan->pesananSparepart->sum('subtotal');

        return view('admin.pembayaran.create', compact('pesanan', 'totalHarga'));
    }

    /**
     * Proses pembayaran dan buat transaksi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pesanan_id'     => 'required|exists:pesanan,id',
            'diskon_nominal' => 'nullable|numeric|min:0',
            'dp'             => 'nullable|numeric|min:0',
            'metode_bayar'   => 'required|in:cash,transfer,debit',
        ], [
            'pesanan_id.required'   => 'Pesanan wajib dipilih.',
            'metode_bayar.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $pesanan = Pesanan::with(['pesananJasa.jasa', 'pesananSparepart'])->findOrFail($request->pesanan_id);

        // Cek duplikat
        if ($pesanan->transaksi) {
            return back()->with('error', 'Pesanan ini sudah memiliki nota pembayaran.');
        }

        DB::beginTransaction();
        try {
            // 1. Generate no_nota using model method
            $noNota = Transaksi::generateNoNota();

            // 2. Calculate totals
            $totalHarga = $pesanan->pesananJasa->sum('subtotal') + $pesanan->pesananSparepart->sum('subtotal');
            $diskonNominal = $request->diskon_nominal ?? 0;
            $afterDiskon = $totalHarga - $diskonNominal;
            $dp = $request->dp ?? 0;
            $sisaBayar = $afterDiskon - $dp;
            $totalBayar = $afterDiskon;

            // Determine status_bayar
            if ($dp > 0 && $sisaBayar > 0) {
                $statusBayar = 'dp';
            } elseif ($sisaBayar <= 0) {
                $statusBayar = 'lunas';
            } else {
                $statusBayar = 'belum_bayar';
            }

            // Note: Auto-reduce sparepart stok sudah dilakukan di PesananController saat pesanan dibuat/diupdate.

            // 4. Create transaksi record
            $transaksi = Transaksi::create([
                'no_nota'        => $noNota,
                'pesanan_id'     => $pesanan->id,
                'tanggal'        => Carbon::now(),
                'total_harga'    => $totalHarga,
                'diskon_persen'  => 0,
                'diskon_nominal' => $diskonNominal,
                'dp'             => $dp,
                'total_bayar'    => $totalBayar,
                'sisa_bayar'     => $sisaBayar,
                'status_bayar'   => $statusBayar,
                'metode_bayar'   => $request->metode_bayar,
            ]);

            DB::commit();

            return redirect()->route('admin.pembayaran.show', $transaksi->id)
                ->with('success', "Pembayaran berhasil. No. Nota: {$noNota}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan detail nota/invoice.
     */
    public function show($id)
    {
        $transaksi = Transaksi::with([
            'pesanan.customer',
            'pesanan.pesananJasa.jasa',
            'pesanan.pesananSparepart.sparepart',
        ])->findOrFail($id);

        return view('admin.pembayaran.show', compact('transaksi'));
    }

    /**
     * Pelunasan sisa tagihan.
     */
    public function pelunasan(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status_bayar === 'lunas') {
            return back()->with('error', 'Pembayaran sudah lunas.');
        }

        DB::beginTransaction();
        try {
            // Update transaksi
            $transaksi->update([
                'dp' => $transaksi->dp + $transaksi->sisa_bayar,
                'sisa_bayar' => 0,
                'status_bayar' => 'lunas',
            ]);

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil dilunasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melunasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan nota untuk print.
     */
    public function printNota($id)
    {
        $transaksi = Transaksi::with([
            'pesanan.customer',
            'pesanan.pesananJasa.jasa',
        ])->findOrFail($id);

        return view('admin.pembayaran.print', compact('transaksi'));
    }
}
