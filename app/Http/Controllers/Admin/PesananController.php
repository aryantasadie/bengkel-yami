<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\PesananJasa;
use App\Models\JasaKaryawan;
use App\Models\Customer;
use App\Models\Jasa;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PesananController extends Controller
{
    /**
     * Tampilkan daftar pesanan dengan filter & search.
     */
    public function index(Request $request)
    {
        $query = Pesanan::with(['customer', 'pesananJasa.jasa', 'pesananSparepart']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $pesanans = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pesanan.index', compact('pesanans'));
    }

    /**
     * Tampilkan form buat pesanan baru.
     */
    public function create()
    {
        $customers = Customer::orderBy('nama')->get();
        $jasas = Jasa::where('is_active', true)->with('spareparts')->get();
        
        $karyawans = Karyawan::where('is_active', true)
            ->withCount(['jasaKaryawan as active_tasks_count' => function ($query) {
                $query->whereIn('status', ['ditugaskan', 'proses'])
                      ->whereHas('pesananJasa.pesanan', function($q) {
                          $q->whereIn('status', ['antrian', 'proses']);
                      });
            }])
            ->get();

        $spareparts = \App\Models\Sparepart::all();

        return view('admin.pesanan.create', compact('customers', 'jasas', 'karyawans', 'spareparts'));
    }

    /**
     * Simpan pesanan baru (complex store logic).
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'               => 'required|exists:customers,id',
            'deskripsi_pekerjaan'       => 'required|string|max:1000',
            'catatan'                   => 'nullable|string|max:500',
            'diskon_persen'             => 'nullable|numeric|min:0|max:100',
            'dp'                        => 'nullable|numeric|min:0',
            'jasas'                     => 'required|array|min:1',
            'jasas.*.jasa_id'           => 'required|exists:jasa,id',
            'jasas.*.karyawan_id'       => 'nullable|exists:karyawan,id',
            'jasas.*.nama_custom'       => 'nullable|string|max:100',
            'jasas.*.harga_custom'      => 'nullable|numeric|min:0',
            'spareparts'                => 'nullable|array',
            'spareparts.*.sparepart_id' => 'required_with:spareparts|exists:sparepart,id',
            'spareparts.*.qty'          => 'required_with:spareparts|integer|min:1',
            'spareparts.*.nama_custom'  => 'nullable|string|max:100',
            'spareparts.*.harga_custom' => 'nullable|numeric|min:0',
        ], [
            'customer_id.required'            => 'Customer wajib dipilih.',
            'deskripsi_pekerjaan.required'    => 'Deskripsi pekerjaan wajib diisi.',
            'jasas.required'                  => 'Minimal pilih satu jasa.',
            'jasas.min'                       => 'Minimal pilih satu jasa.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Generate no_pesanan (PES-YYYYMMDD-XXX)
            $today = Carbon::now()->format('Ymd');
            $lastPesanan = Pesanan::where('no_pesanan', 'like', "PES-{$today}-%")
                ->orderBy('no_pesanan', 'desc')
                ->first();

            if ($lastPesanan) {
                $lastNumber = (int) substr($lastPesanan->no_pesanan, -3);
                $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '001';
            }

            $noPesanan = "PES-{$today}-{$nextNumber}";

            // 2. Create pesanan
            $pesanan = Pesanan::create([
                'no_pesanan'              => $noPesanan,
                'customer_id'             => $request->customer_id,
                'tanggal_masuk'           => Carbon::now(),
                'deskripsi_pekerjaan'     => $request->deskripsi_pekerjaan,
                'catatan'                 => $request->catatan,
                'diskon_persen'           => $request->diskon_persen ?? 0,
                'diskon_sparepart_persen' => $request->diskon_sparepart_persen ?? 0,
                'dp'                      => $request->dp ?? 0,
                'status'                  => 'antrian',
            ]);

            // 3. Create pesanan_jasa entries + jasa_karyawan assignments
            foreach ($request->jasas as $jasaItem) {
                $jasa = Jasa::findOrFail($jasaItem['jasa_id']);
                
                $namaSnapshot = !empty($jasaItem['nama_custom']) ? $jasaItem['nama_custom'] : null;
                $hargaSnapshot = isset($jasaItem['harga_custom']) ? $jasaItem['harga_custom'] : $jasa->harga;

                // Snapshot harga saat ini
                $pesananJasa = PesananJasa::create([
                    'pesanan_id'     => $pesanan->id,
                    'jasa_id'        => $jasa->id,
                    'nama_snapshot'  => $namaSnapshot,
                    'harga_snapshot' => $hargaSnapshot,
                    'qty'            => 1,
                    'subtotal'       => $hargaSnapshot,
                ]);

                // 4. Create jasa_karyawan assignment (if any)
                if (!empty($jasaItem['karyawan_id'])) {
                    JasaKaryawan::create([
                        'pesanan_jasa_id' => $pesananJasa->id,
                        'karyawan_id'     => $jasaItem['karyawan_id'],
                        'status'          => 'ditugaskan',
                    ]);
                }
            }

            if ($request->has('spareparts')) {
                foreach ($request->spareparts as $spItem) {
                    $sparepart = \App\Models\Sparepart::findOrFail($spItem['sparepart_id']);
                    
                    $qtyDibutuhkan = $spItem['qty'];
                    $totalHargaBeli = 0;
                    $sisaKebutuhan = $qtyDibutuhkan;

                    $batches = DB::table('sparepart_batches')
                        ->where('sparepart_id', $sparepart->id)
                        ->where('qty_sisa', '>', 0)
                        ->orderBy('tanggal_masuk', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($sisaKebutuhan <= 0) break;

                        $qtyAmbil = min($batch->qty_sisa, $sisaKebutuhan);
                        $totalHargaBeli += ($qtyAmbil * $batch->harga_beli);
                        $sisaKebutuhan -= $qtyAmbil;

                        DB::table('sparepart_batches')->where('id', $batch->id)->decrement('qty_sisa', $qtyAmbil);
                    }

                    if ($sisaKebutuhan > 0) {
                        $totalHargaBeli += ($sisaKebutuhan * $sparepart->getRawOriginal('harga_beli'));
                    }

                    $avgHargaBeli = $qtyDibutuhkan > 0 ? ($totalHargaBeli / $qtyDibutuhkan) : 0;
                    
                    $namaSnapshot = !empty($spItem['nama_custom']) ? $spItem['nama_custom'] : null;
                    $hargaSnapshot = isset($spItem['harga_custom']) ? $spItem['harga_custom'] : $sparepart->harga_jual;
                    
                    \App\Models\PesananSparepart::create([
                        'pesanan_id'          => $pesanan->id,
                        'sparepart_id'        => $sparepart->id,
                        'nama_snapshot'       => $namaSnapshot,
                        'qty'                 => $spItem['qty'],
                        'harga_beli_snapshot' => $avgHargaBeli,
                        'harga_snapshot'      => $hargaSnapshot,
                        'subtotal'            => $hargaSnapshot * $spItem['qty'],
                    ]);
                    
                    $sparepart->decrement('stok', $spItem['qty']);
                }
            }

            DB::commit();

            return redirect()->route('admin.pesanan.index')
                ->with('success', "Pesanan {$noPesanan} berhasil dibuat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan form edit pesanan (hanya untuk edit sparepart).
     */
    public function edit($id)
    {
        $pesanan = Pesanan::with(['pesananJasa.jasa', 'pesananJasa.jasaKaryawan.karyawan', 'pesananSparepart.sparepart'])->findOrFail($id);

        // Hanya bisa edit jika status antrian atau proses
        if (!in_array($pesanan->status, ['antrian', 'proses'])) {
            return redirect()->route('admin.pesanan.index')->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diedit.');
        }

        $jasas = \App\Models\Jasa::where('is_active', true)->get();
        $karyawans = \App\Models\Karyawan::where('is_active', true)
            ->withCount(['jasaKaryawan as active_tasks_count' => function ($query) {
                $query->whereIn('status', ['ditugaskan', 'proses'])
                      ->whereHas('pesananJasa.pesanan', function($q) {
                          $q->whereIn('status', ['antrian', 'proses']);
                      });
            }])
            ->get();

        $existingSpIds = $pesanan->pesananSparepart->pluck('sparepart_id')->toArray();
        $spareparts = \App\Models\Sparepart::all();

        return view('admin.pesanan.edit', compact('pesanan', 'spareparts', 'jasas', 'karyawans'));
    }

    /**
     * Update data pesanan (khusus update sparepart).
     */
    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        if (!in_array($pesanan->status, ['antrian', 'proses'])) {
            return redirect()->route('admin.pesanan.index')->with('error', 'Pesanan tidak dapat diedit.');
        }

        $request->validate([
            'jasas'                     => 'required|array|min:1',
            'jasas.*.jasa_id'           => 'required|exists:jasa,id',
            'jasas.*.karyawan_id'       => 'nullable|exists:karyawan,id',
            'jasas.*.nama_custom'       => 'nullable|string|max:100',
            'jasas.*.harga_custom'      => 'nullable|numeric|min:0',
            'spareparts'                => 'nullable|array',
            'spareparts.*.sparepart_id' => 'required_with:spareparts|exists:sparepart,id',
            'spareparts.*.qty'          => 'required_with:spareparts|integer|min:1',
            'spareparts.*.nama_custom'  => 'nullable|string|max:100',
            'spareparts.*.harga_custom' => 'nullable|numeric|min:0',
            'deskripsi_pekerjaan'       => 'required|string|max:1000',
            'catatan'                   => 'nullable|string',
            'diskon_persen'             => 'nullable|numeric|min:0|max:100',
            'diskon_sparepart_persen'   => 'nullable|numeric|min:0|max:100',
            'dp'                        => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update pesanan basic fields
            $pesanan->update([
                'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                'catatan'             => $request->catatan,
                'diskon_persen'       => $request->diskon_persen ?? 0,
                'diskon_sparepart_persen' => $request->diskon_sparepart_persen ?? 0,
                'dp'                  => $request->dp ?? 0,
            ]);

            // --- 1. REKONSILIASI JASA ---
            // Hapus semua jasa_karyawan lama
            foreach ($pesanan->pesananJasa as $pj) {
                $pj->jasaKaryawan()->delete();
            }
            // Hapus semua pesanan_jasa lama
            $pesanan->pesananJasa()->delete();

            // Insert ulang Jasa & Karyawan
            foreach ($request->jasas as $jasaItem) {
                $jasa = \App\Models\Jasa::findOrFail($jasaItem['jasa_id']);
                
                $namaSnapshot = !empty($jasaItem['nama_custom']) ? $jasaItem['nama_custom'] : null;
                $hargaSnapshot = isset($jasaItem['harga_custom']) ? $jasaItem['harga_custom'] : $jasa->harga;
                
                $pesananJasa = \App\Models\PesananJasa::create([
                    'pesanan_id'     => $pesanan->id,
                    'jasa_id'        => $jasa->id,
                    'nama_snapshot'  => $namaSnapshot,
                    'harga_snapshot' => $hargaSnapshot,
                    'qty'            => 1,
                    'subtotal'       => $hargaSnapshot,
                ]);

                if (!empty($jasaItem['karyawan_id'])) {
                    \App\Models\JasaKaryawan::create([
                        'pesanan_jasa_id' => $pesananJasa->id,
                        'karyawan_id'     => $jasaItem['karyawan_id'],
                        'status'          => 'ditugaskan',
                    ]);
                }
            }

            // --- 2. REKONSILIASI SPAREPART ---
            $existingSp = $pesanan->pesananSparepart->keyBy('sparepart_id');
            $newSpInput = collect($request->spareparts)->keyBy('sparepart_id');

            // 1. Kembalikan stok untuk sparepart yang dihapus atau dikurangi
            foreach ($existingSp as $spId => $oldItem) {
                if (!$newSpInput->has($spId)) {
                    // Dihapus total
                    $diff = $oldItem->qty;
                    $oldItem->sparepart->increment('stok', $diff);
                    
                    // Kembalikan ke batch terbaru yang stoknya belum penuh (LIFO return)
                    DB::table('sparepart_batches')
                        ->where('sparepart_id', $spId)
                        ->whereRaw('qty_sisa < qty_awal')
                        ->orderBy('id', 'desc')
                        ->limit(1)
                        ->increment('qty_sisa', $diff);

                    $oldItem->delete();
                } else {
                    // Ada di input baru, cek apakah Qty berkurang
                    $newQty = $newSpInput[$spId]['qty'];
                    if ($oldItem->qty > $newQty) {
                        $diff = $oldItem->qty - $newQty;
                        $oldItem->sparepart->increment('stok', $diff);

                        DB::table('sparepart_batches')
                            ->where('sparepart_id', $spId)
                            ->whereRaw('qty_sisa < qty_awal')
                            ->orderBy('id', 'desc')
                            ->limit(1)
                            ->increment('qty_sisa', $diff);
                    }
                }
            }

            // 2. Kurangi stok untuk sparepart yang baru ditambah atau ditambah Qty-nya
            foreach ($newSpInput as $spId => $newItem) {
                $sparepart = \App\Models\Sparepart::findOrFail($spId);
                
                if (!$existingSp->has($spId)) {
                    // Tambahan baru (Potong berjenjang FIFO)
                    $qtyDibutuhkan = $newItem['qty'];
                    $totalHargaBeli = 0;
                    $sisaKebutuhan = $qtyDibutuhkan;

                    $batches = DB::table('sparepart_batches')
                        ->where('sparepart_id', $sparepart->id)
                        ->where('qty_sisa', '>', 0)
                        ->orderBy('tanggal_masuk', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($sisaKebutuhan <= 0) break;
                        $qtyAmbil = min($batch->qty_sisa, $sisaKebutuhan);
                        $totalHargaBeli += ($qtyAmbil * $batch->harga_beli);
                        $sisaKebutuhan -= $qtyAmbil;
                        DB::table('sparepart_batches')->where('id', $batch->id)->decrement('qty_sisa', $qtyAmbil);
                    }

                    if ($sisaKebutuhan > 0) {
                        $totalHargaBeli += ($sisaKebutuhan * $sparepart->getRawOriginal('harga_beli'));
                    }

                    $avgHargaBeli = $qtyDibutuhkan > 0 ? ($totalHargaBeli / $qtyDibutuhkan) : 0;
                    
                    $namaSnapshot = !empty($newItem['nama_custom']) ? $newItem['nama_custom'] : null;
                    $hargaSnapshot = isset($newItem['harga_custom']) ? $newItem['harga_custom'] : $sparepart->harga_jual;

                    \App\Models\PesananSparepart::create([
                        'pesanan_id'          => $pesanan->id,
                        'sparepart_id'        => $sparepart->id,
                        'nama_snapshot'       => $namaSnapshot,
                        'qty'                 => $newItem['qty'],
                        'harga_beli_snapshot' => $avgHargaBeli,
                        'harga_snapshot'      => $hargaSnapshot,
                        'subtotal'            => $hargaSnapshot * $newItem['qty'],
                    ]);
                    $sparepart->decrement('stok', $newItem['qty']);
                } else {
                    // Sudah ada, cek apakah Qty bertambah
                    $oldItem = $existingSp[$spId];
                    $newQty = $newItem['qty'];
                    
                    if ($newQty > $oldItem->qty) {
                        $diff = $newQty - $oldItem->qty;
                        
                        // Potong berjenjang FIFO untuk tambahannya saja
                        $totalHargaBeliTambahan = 0;
                        $sisaKebutuhan = $diff;

                        $batches = DB::table('sparepart_batches')
                            ->where('sparepart_id', $sparepart->id)
                            ->where('qty_sisa', '>', 0)
                            ->orderBy('tanggal_masuk', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();

                        foreach ($batches as $batch) {
                            if ($sisaKebutuhan <= 0) break;
                            $qtyAmbil = min($batch->qty_sisa, $sisaKebutuhan);
                            $totalHargaBeliTambahan += ($qtyAmbil * $batch->harga_beli);
                            $sisaKebutuhan -= $qtyAmbil;
                            DB::table('sparepart_batches')->where('id', $batch->id)->decrement('qty_sisa', $qtyAmbil);
                        }

                        if ($sisaKebutuhan > 0) {
                            $totalHargaBeliTambahan += ($sisaKebutuhan * $sparepart->getRawOriginal('harga_beli'));
                        }

                        // HPP baru = (Total HPP Lama + Total HPP Tambahan) / Qty Baru
                        $oldTotalHPP = $oldItem->qty * $oldItem->harga_beli_snapshot;
                        $newHPP = ($oldTotalHPP + $totalHargaBeliTambahan) / $newQty;
                        
                        $sparepart->decrement('stok', $diff);
                        
                        $namaSnapshot = !empty($newItem['nama_custom']) ? $newItem['nama_custom'] : $oldItem->nama_snapshot;
                        $hargaSnapshot = isset($newItem['harga_custom']) ? $newItem['harga_custom'] : $oldItem->harga_snapshot;

                        // Update data
                        $oldItem->update([
                            'nama_snapshot' => $namaSnapshot,
                            'qty' => $newQty,
                            'harga_beli_snapshot' => $newHPP,
                            'harga_snapshot' => $hargaSnapshot,
                            'subtotal' => $hargaSnapshot * $newQty,
                        ]);
                    } else if ($newQty <= $oldItem->qty) {
                        // Jika berkurang atau tetap, kita update qty dan subtotal
                        $namaSnapshot = !empty($newItem['nama_custom']) ? $newItem['nama_custom'] : $oldItem->nama_snapshot;
                        $hargaSnapshot = isset($newItem['harga_custom']) ? $newItem['harga_custom'] : $oldItem->harga_snapshot;
                        
                        $oldItem->update([
                            'nama_snapshot' => $namaSnapshot,
                            'qty' => $newQty,
                            'harga_snapshot' => $hargaSnapshot,
                            'subtotal' => $hargaSnapshot * $newQty,
                        ]);
                    }
                }
            }

            // Update catatan jika ada
            if ($request->has('catatan')) {
                $pesanan->update(['catatan' => $request->catatan]);
            }

            DB::commit();

            return redirect()->route('admin.pesanan.show', $pesanan->id)
                ->with('success', "Pesanan {$pesanan->no_pesanan} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui pesanan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail pesanan.
     */
    public function show($id)
    {
        $pesanan = Pesanan::with([
            'customer', 
            'pesananJasa.jasa', 
            'pesananJasa.jasaKaryawan.karyawan',
            'pesananSparepart.sparepart',
            'transaksi'
        ])->findOrFail($id);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    /**
     * Cetak Estimasi Pesanan
     */
    public function printEstimasi(Request $request, $id)
    {
        $pesanan = Pesanan::with([
            'customer', 
            'pesananJasa.jasa', 
            'pesananJasa.jasaKaryawan.karyawan',
            'pesananSparepart.sparepart'
        ])->findOrFail($id);

        $withDiskon = $request->query('with_diskon', 1);

        $settings = [
            'nama_bengkel' => \App\Models\PengaturanNota::get('nama_bengkel', 'Bengkel Yami'),
            'alamat_bengkel' => \App\Models\PengaturanNota::get('alamat_bengkel', 'Jl. Contoh No. 123'),
            'no_telp_bengkel' => \App\Models\PengaturanNota::get('no_telp_bengkel', '08123456789'),
            'catatan_kaki' => \App\Models\PengaturanNota::get('catatan_kaki', 'Terima kasih atas kunjungan Anda.'),
        ];

        return view('admin.pesanan.print_estimasi', compact('pesanan', 'withDiskon', 'settings'));
    }

    /**
     * Update status pesanan.
     */
    public function updateStatus(Request $request, $id)
    {
        $pesanan = Pesanan::with('pesananJasa.jasaKaryawan')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:proses,selesai,dibatalkan',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
        ]);

        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            $pesanan->update(['status' => $newStatus]);

            if ($newStatus === 'proses') {
                $pesanan->update(['tanggal_selesai' => null]);
            }

            if ($newStatus === 'selesai') {
                $pesanan->update(['tanggal_selesai' => Carbon::now()]);

                // Update semua jasa_karyawan menjadi selesai
                // (Komisi satuan sekarang dihandle langsung oleh Karyawan/TugasController saat mekanik klik Tandai Selesai)
                foreach ($pesanan->pesananJasa as $pj) {
                    foreach ($pj->jasaKaryawan as $jk) {
                        if($jk->status !== 'selesai') {
                            $jk->update(['status' => 'selesai']);
                            
                            // Tetap berikan komisi jika dipaksa selesai oleh admin,
                            // tapi HANYA jika mekanik belum klik selesai sendiri.
                            $komisiNominal = $pj->harga_snapshot * 0.05;
                            \App\Models\KomisiKaryawan::updateOrCreate(
                                [
                                    'karyawan_id' => $jk->karyawan_id,
                                    'pesanan_jasa_id' => $pj->id,
                                ],
                                [
                                    'nominal_komisi' => $komisiNominal,
                                    'tanggal' => Carbon::now()->toDateString(),
                                ]
                            );
                        }
                    }
                }
            }

            if ($newStatus === 'dibatalkan') {
                // Update semua jasa_karyawan menjadi dibatalkan
                foreach ($pesanan->pesananJasa as $pj) {
                    foreach ($pj->jasaKaryawan as $jk) {
                        $jk->update(['status' => 'dibatalkan']);
                    }
                    // Hapus komisi jika pesanan dibatalkan setelah selesai
                    \App\Models\KomisiKaryawan::where('pesanan_jasa_id', $pj->id)->delete();
                }
                
                // Kembalikan stok sparepart
                foreach ($pesanan->pesananSparepart as $ps) {
                    $ps->sparepart->increment('stok', $ps->qty);
                }
            }

            DB::commit();

            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Update status jasa_karyawan secara individu (Admin Edit).
     */
    public function updateJasaKaryawanStatus(Request $request, $id)
    {
        $jk = JasaKaryawan::with('pesananJasa.pesanan')->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:ditugaskan,proses,selesai',
        ]);

        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            $jk->update(['status' => $newStatus]);

            if ($newStatus === 'selesai') {
                $komisiNominal = $jk->pesananJasa->harga_snapshot * 0.05;
                \App\Models\KomisiKaryawan::updateOrCreate(
                    [
                        'karyawan_id' => $jk->karyawan_id,
                        'pesanan_jasa_id' => $jk->pesanan_jasa_id,
                    ],
                    [
                        'nominal_komisi' => $komisiNominal,
                        'tanggal' => Carbon::now()->toDateString(),
                    ]
                );

                // Cek apakah semua jasa_karyawan di pesanan ini sudah selesai
                $pesanan = $jk->pesananJasa->pesanan;
                $allJasaKaryawan = \App\Models\JasaKaryawan::whereHas('pesananJasa', function ($q) use ($pesanan) {
                    $q->where('pesanan_id', $pesanan->id);
                })->get();

                $allCompleted = $allJasaKaryawan->every(function ($j) {
                    return $j->status === 'selesai';
                });

                if ($allCompleted) {
                    $pesanan->update([
                        'status'           => 'selesai',
                        'tanggal_selesai'  => Carbon::now(),
                    ]);
                }
            } else {
                // Hapus komisi jika dikembalikan ke proses/ditugaskan
                \App\Models\KomisiKaryawan::where('pesanan_jasa_id', $jk->pesanan_jasa_id)
                    ->where('karyawan_id', $jk->karyawan_id)
                    ->delete();

                // Kembalikan status Pesanan utama ke 'proses' jika sebelumnya sudah 'selesai'
                if ($jk->pesananJasa->pesanan->status === 'selesai') {
                    $jk->pesananJasa->pesanan->update([
                        'status' => 'proses',
                        'tanggal_selesai' => null
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Status pengerjaan mekanik berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Hapus pesanan (hanya jika status antrian).
     */
    public function destroy($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        if ($pesanan->status !== 'antrian') {
            return back()->with('error', 'Hanya pesanan dengan status antrian yang bisa dihapus.');
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok sparepart
            foreach ($pesanan->pesananSparepart as $ps) {
                $ps->sparepart->increment('stok', $ps->qty);
            }
            $pesanan->pesananSparepart()->delete();

            // Hapus jasa_karyawan dan pesanan_jasa
            foreach ($pesanan->pesananJasa as $pj) {
                $pj->jasaKaryawan()->delete();
            }
            $pesanan->pesananJasa()->delete();
            $pesanan->delete();

            DB::commit();

            return redirect()->route('admin.pesanan.index')
                ->with('success', 'Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pesanan.');
        }
    }
}
