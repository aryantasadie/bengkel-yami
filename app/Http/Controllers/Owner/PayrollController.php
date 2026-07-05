<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Karyawan;
use App\Models\KomisiKaryawan;
use App\Models\Absensi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Hitung ulang (recalculate) nilai slip gaji dari database (untuk status draft).
     */
    public function recalculate($id)
    {
        $payroll = \App\Models\Payroll::findOrFail($id);

        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Hanya slip gaji berstatus DRAFT yang bisa dihitung ulang.');
        }

        $karyawan = $payroll->karyawan;
        $bulan = $payroll->periode_bulan;
        $tahun = $payroll->periode_tahun;

        // Hitung jumlah hari kerja dalam bulan tersebut (asumsi 26 hari)
        $hariKerja = 26;

        // Snapshot gaji pokok & tunjangan harian
        $gajiPokok = $karyawan->gaji_pokok;
        $tunjanganHarian = $karyawan->tunjangan ?? 0;

        // Hitung total komisi dari komisi_karyawan untuk periode tersebut
        $totalKomisi = \App\Models\KomisiKaryawan::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('nominal_komisi');

        // Hitung jumlah kehadiran (Hadir)
        $jumlahHadir = \App\Models\Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'hadir')
            ->count();

        // Hitung jumlah bolos tanpa keterangan (HANYA Alpha)
        $jumlahAlpha = \App\Models\Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'alpha')
            ->count();

        // Potongan Absen HANYA dihitung dari jumlah Alpha
        $rateHarian = $hariKerja > 0 ? $gajiPokok / $hariKerja : 0;
        $potonganAbsen = $jumlahAlpha * $rateHarian;

        // Tunjangan harian HANYA diberikan sesuai hari kerja yang ia benar-benar HADIR
        $tunjanganTotal = $tunjanganHarian * $jumlahHadir;

        // Lembur (ambil yang ada di database sekarang, jangan di reset ke 0 kalau sudah diubah manual)
        $lembur = $payroll->total_lembur;

        // Hitung gaji bersih
        $gajiBersih = $gajiPokok + $tunjanganTotal + $totalKomisi + $lembur - $potonganAbsen;
        $gajiBersih = max($gajiBersih, 0);

        $payroll->update([
            'gaji_pokok_snapshot' => $gajiPokok,
            'tunjangan_snapshot'  => $tunjanganTotal,
            'total_komisi'        => $totalKomisi,
            'potongan_absen'      => $potonganAbsen,
            'gaji_bersih'         => $gajiBersih,
        ]);

        return back()->with('success', 'Slip gaji berhasil dihitung ulang (di-refresh) dengan data terbaru!');
    }

    /**
     * Tampilkan daftar payroll.
     */
    public function index(Request $request)
    {
        $query = Payroll::with('karyawan');

        if ($request->filled('bulan')) {
            $query->where('periode_bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('periode_tahun', $request->tahun);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->latest('periode_tahun')
            ->latest('periode_bulan')
            ->paginate(15)
            ->withQueryString();

        // Periode yang tersedia untuk filter
        $periodes = \App\Models\Payroll::select('periode_bulan as bulan', 'periode_tahun as tahun')
            ->distinct()
            ->orderByDesc('periode_tahun')
            ->orderByDesc('periode_bulan')
            ->get();

        $karyawans = \App\Models\Karyawan::where('is_active', true)->get();

        return view('owner.payroll.index', compact('payrolls', 'periodes', 'karyawans'));
    }

    /**
     * Generate payroll untuk bulan/tahun tertentu.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2099',
        ], [
            'bulan.required' => 'Bulan wajib dipilih.',
            'tahun.required' => 'Tahun wajib diisi.',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $karyawanIds = $request->input('karyawan_ids', []);

        if (empty($karyawanIds)) {
            return back()->with('error', 'Pilih minimal satu karyawan.');
        }

        // Cek apakah sudah ada payroll untuk karyawan yang dipilih pada periode ini
        $existingKaryawans = \App\Models\Payroll::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->whereIn('karyawan_id', $karyawanIds)
            ->exists();

        if ($existingKaryawans) {
            return back()->with('error', "Salah satu atau beberapa karyawan yang dipilih sudah memiliki payroll untuk periode {$bulan}/{$tahun}. Hapus payroll mereka terlebih dahulu jika ingin men-generate ulang.");
        }

        DB::beginTransaction();
        try {
            // Get karyawan yang dipilih dan aktif
            $karyawans = \App\Models\Karyawan::whereIn('id', $karyawanIds)->where('is_active', true)->get();

            if ($karyawans->isEmpty()) {
                return back()->with('error', 'Karyawan yang dipilih tidak valid atau tidak aktif.');
            }

            // Hitung jumlah hari kerja dalam bulan tersebut (asumsi 26 hari)
            $hariKerja = 26;

            foreach ($karyawans as $karyawan) {
                // Snapshot gaji pokok & tunjangan harian
                $gajiPokok = $karyawan->gaji_pokok;
                $tunjanganHarian = $karyawan->tunjangan ?? 0;

                // Hitung total komisi dari komisi_karyawan untuk periode tersebut
                $totalKomisi = \App\Models\KomisiKaryawan::where('karyawan_id', $karyawan->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('nominal_komisi');

                // Hitung jumlah kehadiran (Hadir)
                $jumlahHadir = \App\Models\Absensi::where('karyawan_id', $karyawan->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'hadir')
                    ->count();

                // Hitung jumlah bolos tanpa keterangan (HANYA Alpha)
                $jumlahAlpha = \App\Models\Absensi::where('karyawan_id', $karyawan->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'alpha')
                    ->count();

                // Potongan Absen HANYA dihitung dari jumlah Alpha
                $rateHarian = $hariKerja > 0 ? $gajiPokok / $hariKerja : 0;
                $potonganAbsen = $jumlahAlpha * $rateHarian;

                // Tunjangan harian HANYA diberikan sesuai hari kerja yang ia benar-benar HADIR
                $tunjanganTotal = $tunjanganHarian * $jumlahHadir;

                // Lembur (default 0, bisa ditambahkan manual nanti)
                $lembur = 0;

                // Hitung gaji bersih
                $gajiBersih = $gajiPokok + $tunjanganTotal + $totalKomisi + $lembur - $potonganAbsen;

                // Pastikan gaji bersih tidak negatif
                $gajiBersih = max($gajiBersih, 0);

                Payroll::create([
                    'karyawan_id'         => $karyawan->id,
                    'periode_bulan'       => $bulan,
                    'periode_tahun'       => $tahun,
                    'gaji_pokok_snapshot' => $gajiPokok,
                    'tunjangan_snapshot'  => $tunjanganTotal,
                    'total_komisi'        => $totalKomisi,
                    'total_lembur'        => $lembur,
                    'potongan_absen'      => $potonganAbsen,
                    'potongan_lain'       => 0,
                    'gaji_bersih'         => $gajiBersih,
                    'status'              => 'draft',
                ]);
            }

            DB::commit();

            return redirect()->route('owner.payroll.index')
                ->with('success', "Payroll periode {$bulan}/{$tahun} berhasil di-generate untuk {$karyawans->count()} karyawan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate payroll: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail payroll karyawan.
     */
    public function show($id)
    {
        $payroll = Payroll::with('karyawan')->findOrFail($id);

        $rincianKomisi = KomisiKaryawan::with(['pesananJasa.jasa', 'pesananJasa.pesanan'])
            ->where('karyawan_id', $payroll->karyawan_id)
            ->whereMonth('tanggal', $payroll->periode_bulan)
            ->whereYear('tanggal', $payroll->periode_tahun)
            ->get();

        return view('owner.payroll.show', compact('payroll', 'rincianKomisi'));
    }

    /**
     * Approve payroll (ubah status ke final).
     */
    public function approve($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Hanya payroll draft yang bisa di-approve.');
        }

        $payroll->update(['status' => 'final']);

        return redirect()->route('owner.payroll.show', $id)
            ->with('success', 'Payroll berhasil di-approve.');
    }

    /**
     * Bayar payroll (ubah status ke dibayar + catat pengeluaran).
     */
    public function pay($id)
    {
        $payroll = Payroll::with('karyawan')->findOrFail($id);

        if ($payroll->status !== 'final') {
            return back()->with('error', 'Hanya payroll yang sudah di-approve yang bisa dibayar.');
        }

        DB::beginTransaction();
        try {
            $payroll->update([
                'status'        => 'dibayar',
                'tanggal_bayar' => Carbon::now(),
            ]);

            // Catat pengeluaran gaji
            Pengeluaran::create([
                'tanggal'    => Carbon::now()->toDateString(),
                'kategori'   => 'gaji',
                'nominal'    => $payroll->gaji_bersih,
                'deskripsi' => "Gaji karyawan: {$payroll->karyawan->nama} - Periode {$payroll->periode_bulan}/{$payroll->periode_tahun}",
            ]);

            DB::commit();

            return redirect()->route('owner.payroll.show', $id)
                ->with('success', 'Payroll berhasil dibayarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Batal Approve payroll (ubah status kembali ke draft).
     */
    public function unapprove($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'final') {
            return back()->with('error', 'Hanya payroll berstatus Final yang bisa di-batal-approve.');
        }

        $payroll->update(['status' => 'draft']);

        return redirect()->route('owner.payroll.show', $id)
            ->with('success', 'Payroll dikembalikan ke status Draft.');
    }

    /**
     * Batal Bayar payroll (ubah status ke final + hapus pengeluaran).
     */
    public function unpay($id)
    {
        $payroll = Payroll::with('karyawan')->findOrFail($id);

        if ($payroll->status !== 'dibayar') {
            return back()->with('error', 'Hanya payroll berstatus Dibayar yang bisa dibatalkan pembayarannya.');
        }

        DB::beginTransaction();
        try {
            $payroll->update([
                'status'        => 'final',
                'tanggal_bayar' => null,
            ]);

            // Hapus pengeluaran terkait
            $deskripsi = "Gaji karyawan: {$payroll->karyawan->nama} - Periode {$payroll->periode_bulan}/{$payroll->periode_tahun}";
            Pengeluaran::where('kategori', 'gaji')
                ->where('nominal', $payroll->gaji_bersih)
                ->where('deskripsi', $deskripsi)
                ->delete();

            DB::commit();

            return redirect()->route('owner.payroll.show', $id)
                ->with('success', 'Pembayaran dibatalkan. Catatan pengeluaran terkait telah dihapus dari Arus Kas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data payroll.
     */
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status === 'dibayar') {
            return back()->with('error', 'Payroll berstatus Dibayar tidak bisa dihapus. Silakan batalkan pembayaran terlebih dahulu.');
        }

        $payroll->delete();

        return redirect()->route('owner.payroll.index')
            ->with('success', 'Data payroll berhasil dihapus.');
    }
}
