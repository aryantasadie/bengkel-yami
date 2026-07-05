<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Tampilkan history absensi.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $karyawan = Karyawan::findOrFail($user->karyawan_id);

        $query = Absensi::where('karyawan_id', $karyawan->id);

        // Filter bulan
        if ($request->filled('bulan')) {
            $bulan = Carbon::parse($request->bulan);
            $query->whereMonth('tanggal', $bulan->month)
                  ->whereYear('tanggal', $bulan->year);
        }

        $absensis = $query->latest('tanggal')->paginate(15)->withQueryString();

        // Absensi hari ini (untuk tombol clock in/out)
        $absensiToday = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        return view('karyawan.absensi.index', compact('absensis', 'absensiToday', 'karyawan'));
    }

    /**
     * Clock in - catat jam masuk.
     */
    public function clockIn()
    {
        $user = auth()->user();
        $karyawan = Karyawan::findOrFail($user->karyawan_id);
        $today = Carbon::today();

        // Cek apakah sudah absen hari ini
        $existingAbsensi = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingAbsensi) {
            return back()->with('error', 'Anda sudah melakukan clock in hari ini.');
        }

        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => $today,
            'jam_masuk'   => Carbon::now()->format('H:i:s'),
            'status'      => 'hadir',
        ]);

        return back()->with('success', 'Clock in berhasil pada ' . Carbon::now()->format('H:i'));
    }

    /**
     * Clock out - catat jam keluar.
     */
    public function clockOut()
    {
        $user = auth()->user();
        $karyawan = Karyawan::findOrFail($user->karyawan_id);
        $today = Carbon::today();

        $absensi = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absensi) {
            return back()->with('error', 'Anda belum melakukan clock in hari ini.');
        }

        if ($absensi->jam_keluar) {
            return back()->with('error', 'Anda sudah melakukan clock out hari ini.');
        }

        $absensi->update([
            'jam_keluar' => Carbon::now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Clock out berhasil pada ' . Carbon::now()->format('H:i'));
    }
}
