<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DummyAbcSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Karyawan 'abc'
        $karyawan = Karyawan::updateOrCreate(
            ['nik' => 'KRY-ABC'],
            [
                'nama' => 'abc',
                'tanggal_lahir' => '1995-01-01',
                'alamat' => 'Jl. ABC No. 123',
                'tanggal_masuk' => '2025-01-01',
                'jabatan' => 'Mekanik Senior',
                'gaji_pokok' => 3000000,
                'tunjangan' => 500000,
                'is_active' => true,
            ]
        );

        // 2. Create User 'abc'
        User::updateOrCreate(
            ['username' => 'abc'],
            [
                'karyawan_id' => $karyawan->id,
                'password' => Hash::make('password'),
                'nama' => 'abc',
                'role' => 'karyawan',
                'is_active' => true,
            ]
        );

        // 3. Generate Absensi
        Absensi::where('karyawan_id', $karyawan->id)->delete();
        $absensis = [];

        // Data Bulan Lalu (Mei 2026)
        // Libur Minggu. Sabtu setengah hari. Ada beberapa hari bolong (tgl 15-18 Mei)
        $startDateMay = Carbon::create(2026, 5, 1);
        for ($day = 1; $day <= 31; $day++) {
            $date = $startDateMay->copy()->addDays($day - 1);
            if ($date->dayOfWeek === Carbon::SUNDAY) continue;
            
            // Simulasi bolong (Alpha) tanggal 15, 16, 18 Mei (17 Mei adalah Minggu)
            if ($day >= 15 && $day <= 18) continue;

            $jamKeluar = ($date->dayOfWeek === Carbon::SATURDAY) ? '13:00:00' : '16:00:00';
            
            $absensis[] = [
                'karyawan_id' => $karyawan->id,
                'tanggal' => $date->format('Y-m-d'),
                'jam_masuk' => '09:00:00',
                'jam_keluar' => $jamKeluar,
                'status' => 'hadir',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Data Bulan Ini (Juni 2026) sampai hari ini (16 Juni)
        $startDateJune = Carbon::create(2026, 6, 1);
        $today = Carbon::today();
        
        for ($date = $startDateJune->copy(); $date->lte($today); $date->addDay()) {
            if ($date->dayOfWeek === Carbon::SUNDAY) continue;

            $jamKeluar = ($date->dayOfWeek === Carbon::SATURDAY) ? '13:00:00' : '16:00:00';
            
            $absensis[] = [
                'karyawan_id' => $karyawan->id,
                'tanggal' => $date->format('Y-m-d'),
                'jam_masuk' => '09:00:00',
                'jam_keluar' => $jamKeluar,
                'status' => 'hadir',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Absensi::insert($absensis);
    }
}
