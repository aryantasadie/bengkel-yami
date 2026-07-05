<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SparepartController extends Controller
{
    /**
     * Tampilkan daftar sparepart dengan indikator stok rendah.
     */
    public function index(Request $request)
    {
        $query = Sparepart::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }

        $spareparts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.sparepart.index', compact('spareparts'));
    }

    /**
     * Tampilkan form tambah sparepart.
     */
    public function create()
    {
        return view('admin.sparepart.create');
    }

    /**
     * Simpan sparepart baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'satuan'           => 'required|string|max:50',
            'total_harga_beli' => 'required|numeric|min:0',
            'harga_jual'       => 'required|numeric|min:0',
            'stok'             => 'required|integer|min:0',
            'stok_minimum'     => 'required|integer|min:0',
        ], [
            'nama.required'             => 'Nama sparepart wajib diisi.',
            'satuan.required'           => 'Satuan wajib diisi.',
            'total_harga_beli.required' => 'Total harga beli wajib diisi.',
            'harga_jual.required'       => 'Harga jual wajib diisi.',
            'stok.required'             => 'Stok wajib diisi.',
            'stok_minimum.required'     => 'Stok minimum wajib diisi.',
        ]);

        // Kalkulasi harga satuan
        $harga_beli_satuan = 0;
        if ($request->stok > 0) {
            $harga_beli_satuan = $request->total_harga_beli / $request->stok;
        } else {
            $harga_beli_satuan = $request->total_harga_beli; // Jika stok 0, anggap inputan adalah harga satuan dasar
        }

        $sparepart = Sparepart::create([
            'nama' => $request->nama,
            'satuan' => $request->satuan,
            'harga_beli' => $harga_beli_satuan,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'stok_minimum' => $request->stok_minimum
        ]);

        if ($request->stok > 0) {
            
            \Illuminate\Support\Facades\DB::table('sparepart_batches')->insert([
                'sparepart_id' => $sparepart->id,
                'qty_awal' => $request->stok,
                'qty_sisa' => $request->stok,
                'harga_beli' => $harga_beli_satuan,
                'tanggal_masuk' => \Carbon\Carbon::now()->toDateString(),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            \App\Models\Pengeluaran::create([
                'tanggal'    => \Carbon\Carbon::now()->toDateString(),
                'deskripsi'  => "Pembelian awal (Stok): {$sparepart->nama} ({$request->stok} {$sparepart->satuan})",
                'kategori'   => 'restock_sparepart',
                'nominal'    => $request->total_harga_beli,
                'keterangan' => 'Pembelian stok awal saat tambah barang baru',
            ]);
        }

        return redirect()->route('admin.sparepart.index')
            ->with('success', 'Sparepart berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail sparepart.
     */
    public function show($id)
    {
        $sparepart = Sparepart::findOrFail($id);

        return view('admin.sparepart.show', compact('sparepart'));
    }

    /**
     * Tampilkan form edit sparepart.
     */
    public function edit($id)
    {
        $sparepart = Sparepart::findOrFail($id);

        return view('admin.sparepart.edit', compact('sparepart'));
    }

    /**
     * Update data sparepart.
     */
    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'nama'           => 'required|string|max:255',
            'satuan'         => 'required|string|max:50',
            'harga_beli'     => 'required|numeric|min:0',
            'harga_jual'     => 'required|numeric|min:0',
            'stok'           => 'required|integer|min:0',
            'stok_minimum'   => 'required|integer|min:0',
        ], [
            'nama.required'           => 'Nama sparepart wajib diisi.',
        ]);

        $sparepart->update($request->only([
            'nama', 'satuan', 'harga_beli', 'harga_jual', 'stok', 'stok_minimum'
        ]));

        // UPDATE OTOMATIS: Pesanan yang masih Antrian / Proses
        $pendingPesananIds = \App\Models\Pesanan::whereIn('status', ['antrian', 'proses'])->pluck('id');
        
        if ($pendingPesananIds->isNotEmpty()) {
            $pendingItems = \App\Models\PesananSparepart::whereIn('pesanan_id', $pendingPesananIds)
                ->where('sparepart_id', $sparepart->id)
                ->get();
            
            foreach ($pendingItems as $item) {
                $item->update([
                    'harga_snapshot' => $request->harga_jual,
                    'subtotal'       => $request->harga_jual * $item->qty
                ]);
            }
        }

        return redirect()->route('admin.sparepart.index')
            ->with('success', 'Data sparepart berhasil diperbarui.');
    }

    /**
     * Restock sparepart + catat pengeluaran.
     */
    public function restock(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'jumlah'           => 'required|integer|min:1',
            'total_harga_beli' => 'required|numeric|min:0',
            'harga_jual'       => 'required|numeric|min:0',
            'keterangan'       => 'nullable|string|max:500',
        ], [
            'jumlah.required'           => 'Jumlah restock wajib diisi.',
            'total_harga_beli.required' => 'Total harga beli wajib diisi.',
            'harga_jual.required'       => 'Harga jual wajib diisi.',
        ]);

        $harga_beli_satuan = $request->total_harga_beli / $request->jumlah;

        DB::beginTransaction();
        try {
            // Create new batch
            DB::table('sparepart_batches')->insert([
                'sparepart_id' => $sparepart->id,
                'qty_awal' => $request->jumlah,
                'qty_sisa' => $request->jumlah,
                'harga_beli' => $harga_beli_satuan,
                'tanggal_masuk' => Carbon::now()->toDateString(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Update stok total, harga beli terakhir, & harga jual master
            $sparepart->increment('stok', $request->jumlah);
            $sparepart->update([
                'harga_beli' => $harga_beli_satuan,
                'harga_jual' => $request->harga_jual
            ]);

            // UPDATE OTOMATIS: Pesanan yang masih Antrian / Proses
            // Cari pesanan_sparepart yang terhubung ke pesanan aktif
            $pendingPesananIds = \App\Models\Pesanan::whereIn('status', ['antrian', 'proses'])->pluck('id');
            
            if ($pendingPesananIds->isNotEmpty()) {
                // Ambil item pesanan_sparepart terkait
                $pendingItems = \App\Models\PesananSparepart::whereIn('pesanan_id', $pendingPesananIds)
                    ->where('sparepart_id', $sparepart->id)
                    ->get();
                
                foreach ($pendingItems as $item) {
                    $item->update([
                        'harga_snapshot' => $request->harga_jual,
                        'subtotal'       => $request->harga_jual * $item->qty
                    ]);
                }
            }

            // Catat pengeluaran
            Pengeluaran::create([
                'tanggal'    => Carbon::now()->toDateString(),
                'deskripsi'  => "Restock sparepart: {$sparepart->nama} ({$request->jumlah} {$sparepart->satuan})",
                'kategori'   => 'restock_sparepart',
                'nominal'    => $request->total_harga_beli,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return redirect()->route('admin.sparepart.index')
                ->with('success', "Restock {$request->jumlah} {$sparepart->satuan} berhasil.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan restock: ' . $e->getMessage());
        }
    }

    /**
     * Hapus sparepart jika tidak ada relasi.
     */
    public function destroy($id)
    {
        $sparepart = Sparepart::findOrFail($id);

        if ($sparepart->jasa()->exists()) {
            return back()->with('error', 'Sparepart tidak bisa dihapus karena digunakan dalam jasa.');
        }

        $sparepart->delete();

        return redirect()->route('admin.sparepart.index')
            ->with('success', 'Sparepart berhasil dihapus.');
    }
}
