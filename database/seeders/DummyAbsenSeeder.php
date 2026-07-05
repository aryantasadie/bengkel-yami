<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use Carbon\Carbon;

class DummyAbsenSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanId = 6;

        Absensi::where('karyawan_id', $karyawanId)->delete();

        $absensis = [];

        // Data Bulan Lalu: Mei 2026 (Absen tgl 26-30 Mei)
        $startDateMay = Carbon::create(2026, 5, 1);
        for ($day = 1; $day <= 31; $day++) {
            $date = $startDateMay->copy()->addDays($day - 1);
            if ($date->dayOfWeek === Carbon::SUNDAY) continue;
            if ($day >= 26 && $day <= 30) continue;

            $absensis[] = [
                'karyawan_id' => $karyawanId,
                'tanggal' => $date->format('Y-m-d'),
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '17:00:00',
                'status' => 'hadir',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Data Bulan Ini: Juni 2026
        $startDateJune = Carbon::create(2026, 6, 1);
        for ($day = 1; $day <= 15; $day++) {
            $date = $startDateJune->copy()->addDays($day - 1);
            if ($date->dayOfWeek === Carbon::SUNDAY) continue;

            $absensis[] = [
                'karyawan_id' => $karyawanId,
                'tanggal' => $date->format('Y-m-d'),
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '17:00:00',
                'status' => 'hadir',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Absensi::insert($absensis);
    }
}
