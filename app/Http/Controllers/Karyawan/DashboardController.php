<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\JasaKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard karyawan.
     */
    public function index()
    {
        $user = auth()->user();
        $karyawan = Karyawan::find($user->karyawan_id);
        
        if (!$karyawan) {
            return redirect()->route('login')->withErrors(['username' => 'Akun ini tidak tertaut dengan data Karyawan.']);
        }

        $today = Carbon::today();

        // Tugas hari ini (semua tugas yang belum selesai)
        $tugasHariIni = JasaKaryawan::where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['ditugaskan', 'proses'])
            ->count();

        // Tugas selesai hari ini
        $tugasSelesai = JasaKaryawan::where('karyawan_id', $karyawan->id)
            ->where('status', 'selesai')
            ->whereDate('updated_at', $today)
            ->count();

        // Absensi hari ini
        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Daftar tugas yang ditugaskan
        $tugasList = JasaKaryawan::with(['pesananJasa.pesanan.customer', 'pesananJasa.jasa'])
            ->where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['ditugaskan', 'proses'])
            ->latest()
            ->get();

        return view('karyawan.dashboard', compact(
            'karyawan',
            'tugasHariIni',
            'tugasSelesai',
            'absensiHariIni',
            'tugasList'
        ));
    }
}
