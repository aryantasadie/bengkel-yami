<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Logistik;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogistikController extends Controller
{
    /**
     * Tampilkan daftar logistik dengan indikator stok rendah.
     */
    public function index(Request $request)
    {
        $query = Logistik::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }

        $logistiks = $query->latest()->paginate(15)->withQueryString();

        return view('admin.logistik.index', compact('logistiks'));
    }

    /**
     * Tampilkan form tambah logistik.
     */
    public function create()
    {
        return view('admin.logistik.create');
    }

    /**
     * Simpan logistik baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'satuan'           => 'required|string|max:50',
            'total_harga_beli' => 'required|numeric|min:0',
            'stok'             => 'required|integer|min:0',
            'stok_minimum'     => 'required|integer|min:0',
        ], [
            'nama.required'             => 'Nama barang wajib diisi.',
            'satuan.required'           => 'Satuan wajib diisi.',
            'total_harga_beli.required' => 'Total harga beli wajib diisi.',
            'stok.required'             => 'Stok wajib diisi.',
            'stok_minimum.required'     => 'Stok minimum wajib diisi.',
        ]);

        $harga_beli_satuan = 0;
        if ($request->stok > 0) {
            $harga_beli_satuan = $request->total_harga_beli / $request->stok;
        } else {
            $harga_beli_satuan = $request->total_harga_beli;
        }

        $logistik = Logistik::create([
            'nama' => $request->nama,
            'satuan' => $request->satuan,
            'harga_beli' => $harga_beli_satuan,
            'stok' => $request->stok,
            'stok_minimum' => $request->stok_minimum
        ]);

        if ($request->stok > 0) {
            \Illuminate\Support\Facades\DB::table('logistik_batches')->insert([
                'logistik_id' => $logistik->id,
                'qty_awal' => $request->stok,
                'qty_sisa' => $request->stok,
                'harga_beli' => $harga_beli_satuan,
                'tanggal_masuk' => \Carbon\Carbon::now()->toDateString(),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            \App\Models\Pengeluaran::create([
                'tanggal'    => \Carbon\Carbon::now()->toDateString(),
                'deskripsi'  => "Pembelian awal (Stok): {$logistik->nama} ({$request->stok} {$logistik->satuan})",
                'kategori'   => 'restock_logistik',
                'nominal'    => $request->total_harga_beli,
                'keterangan' => 'Pembelian stok awal saat tambah barang baru',
            ]);
        }

        return redirect()->route('admin.logistik.index')
            ->with('success', 'Barang logistik berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail logistik.
     */
    public function show($id)
    {
        $logistik = Logistik::findOrFail($id);

        return view('admin.logistik.show', compact('logistik'));
    }

    /**
     * Tampilkan form edit logistik.
     */
    public function edit($id)
    {
        $logistik = Logistik::findOrFail($id);

        return view('admin.logistik.edit', compact('logistik'));
    }

    /**
     * Update data logistik.
     */
    public function update(Request $request, $id)
    {
        $logistik = Logistik::findOrFail($id);

        $request->validate([
            'nama'         => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'harga_beli'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
        ], [
            'nama.required'        => 'Nama barang wajib diisi.',
            'satuan.required'      => 'Satuan wajib diisi.',
            'harga_beli.required'  => 'Harga beli wajib diisi.',
        ]);

        $logistik->update($request->only([
            'nama', 'satuan',
            'harga_beli', 'stok', 'stok_minimum'
        ]));

        return redirect()->route('admin.logistik.index')
            ->with('success', 'Data logistik berhasil diperbarui.');
    }

    /**
     * Restock logistik + catat pengeluaran.
     */
    public function restock(Request $request, $id)
    {
        $logistik = Logistik::findOrFail($id);

        $request->validate([
            'jumlah'           => 'required|integer|min:1',
            'total_harga_beli' => 'required|numeric|min:0',
            'keterangan'       => 'nullable|string|max:500',
        ], [
            'jumlah.required'           => 'Jumlah restock wajib diisi.',
            'total_harga_beli.required' => 'Total harga beli wajib diisi.',
        ]);

        $harga_beli_satuan = $request->total_harga_beli / $request->jumlah;

        DB::beginTransaction();
        try {
            // Create new batch
            DB::table('logistik_batches')->insert([
                'logistik_id' => $logistik->id,
                'qty_awal' => $request->jumlah,
                'qty_sisa' => $request->jumlah,
                'harga_beli' => $harga_beli_satuan,
                'tanggal_masuk' => Carbon::now()->toDateString(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Update stok total & harga beli master
            $logistik->increment('stok', $request->jumlah);
            $logistik->update([
                'harga_beli' => $harga_beli_satuan
            ]);

            // Catat pengeluaran
            // Catat pengeluaran
            Pengeluaran::create([
                'tanggal'    => Carbon::now()->toDateString(),
                'deskripsi'  => "Restock logistik: {$logistik->nama} ({$request->jumlah} {$logistik->satuan})",
                'kategori'   => 'restock_logistik',
                'nominal'    => $request->total_harga_beli,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return redirect()->route('admin.logistik.index')
                ->with('success', "Restock {$request->jumlah} {$logistik->satuan} berhasil.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan restock: ' . $e->getMessage());
        }
    }

    /**
     * Kurangi stok logistik 1 per 1 (Pemakaian)
     */
    public function decrease(Request $request, $id)
    {
        $logistik = Logistik::findOrFail($id);

        if ($logistik->stok <= 0) {
            return back()->with('error', 'Stok logistik sudah habis, tidak bisa dikurangi.');
        }

        DB::beginTransaction();
        try {
            // Potong stok utama
            $logistik->decrement('stok', 1);

            // FIFO logic untuk mengurangi batch
            $qtyToDeduct = 1;
            $batches = DB::table('logistik_batches')
                ->where('logistik_id', $logistik->id)
                ->where('qty_sisa', '>', 0)
                ->orderBy('tanggal_masuk', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($batches as $batch) {
                if ($qtyToDeduct <= 0) break;

                $ambil = min($batch->qty_sisa, $qtyToDeduct);
                DB::table('logistik_batches')
                    ->where('id', $batch->id)
                    ->decrement('qty_sisa', $ambil);

                $qtyToDeduct -= $ambil;
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'new_stok' => $logistik->stok
                ]);
            }

            return back(); // Silent reload without success popup
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal mencatat pemakaian logistik: ' . $e->getMessage());
        }
    }

    /**
     * Hapus logistik jika tidak ada relasi.
     */
    public function destroy($id)
    {
        $logistik = Logistik::findOrFail($id);

        $logistik->delete();

        return redirect()->route('admin.logistik.index')
            ->with('success', 'Barang logistik berhasil dihapus.');
    }
}
