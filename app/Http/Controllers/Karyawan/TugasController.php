<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\JasaKaryawan;
use App\Models\Karyawan;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TugasController extends Controller
{
    /**
     * Tampilkan daftar tugas karyawan yang sedang login.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $karyawan = Karyawan::findOrFail($user->karyawan_id);

        $baseQuery = JasaKaryawan::with(['pesananJasa.pesanan.customer', 'pesananJasa.jasa'])
            ->where('karyawan_id', $karyawan->id);

        $tugasBaru = (clone $baseQuery)->where('status', 'ditugaskan')->latest()->get();
        $tugasProses = (clone $baseQuery)->where('status', 'proses')->latest()->get();
        
        // Riwayat selesai bulan ini
        $tugasSelesai = (clone $baseQuery)
            ->where('status', 'selesai')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->latest('updated_at')
            ->get();

        return view('karyawan.tugas.index', compact('tugasBaru', 'tugasProses', 'tugasSelesai', 'karyawan'));
    }

    /**
     * Update status tugas (jasa_karyawan).
     * Jika semua jasa_karyawan untuk pesanan selesai, update pesanan ke selesai.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();
        $karyawan = Karyawan::findOrFail($user->karyawan_id);

        $jasaKaryawan = JasaKaryawan::where('id', $id)
            ->where('karyawan_id', $karyawan->id)
            ->firstOrFail();

        // Karyawan tidak boleh mengubah yang sudah selesai
        if ($jasaKaryawan->status === 'selesai') {
            return back()->with('error', 'Tugas yang sudah selesai tidak dapat diubah statusnya lagi.');
        }

        $request->validate([
            'status' => 'required|in:proses,selesai',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            $jasaKaryawan->update(['status' => $request->status]);

            $pesananJasa = $jasaKaryawan->pesananJasa;
            $pesanan = $pesananJasa->pesanan;

            // Jika status menjadi proses, update status pesanan utama menjadi proses (jika sebelumnya antrian)
            if ($request->status === 'proses' && $pesanan->status === 'antrian') {
                $pesanan->update(['status' => 'proses']);
            }

            // Jika status selesai, cek apakah semua jasa_karyawan di pesanan ini sudah selesai
            if ($request->status === 'selesai') {
                // Generate komisi (5% dari harga_snapshot jasa)
                $komisiNominal = $pesananJasa->harga_snapshot * 0.05;
                \App\Models\KomisiKaryawan::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'pesanan_jasa_id' => $pesananJasa->id,
                    ],
                    [
                        'nominal_komisi' => $komisiNominal,
                        'tanggal' => Carbon::now()->toDateString(),
                    ]
                );

                // Cek semua jasa_karyawan untuk pesanan ini
                $allJasaKaryawan = JasaKaryawan::whereHas('pesananJasa', function ($q) use ($pesanan) {
                    $q->where('pesanan_id', $pesanan->id);
                })->get();

                $allCompleted = $allJasaKaryawan->every(function ($jk) {
                    return $jk->status === 'selesai';
                });

                if ($allCompleted) {
                    $pesanan->update([
                        'status'           => 'selesai',
                        'tanggal_selesai'  => Carbon::now(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Status tugas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status tugas: ' . $e->getMessage());
        }
    }
}
