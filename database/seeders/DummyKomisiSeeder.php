<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Jasa;
use App\Models\Pesanan;
use App\Models\PesananJasa;
use App\Models\JasaKaryawan;
use App\Models\KomisiKaryawan;
use Carbon\Carbon;

class DummyKomisiSeeder extends Seeder
{
    public function run()
    {
        $karyawanAsd = Karyawan::where('nama', 'like', '%asd%')->first();
        $karyawanAbc = Karyawan::where('nama', 'like', '%abc%')->first();

        if (!$karyawanAsd || !$karyawanAbc) {
            echo "Karyawan asd atau abc tidak ditemukan. Pastikan seeder dijalankan setelah data karyawan ada.\n";
            return;
        }

        // Cari atau buat jasa
        $jasaOli = Jasa::firstOrCreate(['nama_jasa' => 'Ganti Oli'], ['harga' => 50000, 'deskripsi' => 'Ganti Oli Mesin']);
        $jasaServis = Jasa::firstOrCreate(['nama_jasa' => 'Servis Rutin'], ['harga' => 150000, 'deskripsi' => 'Servis Berkala']);
        $jasaVBelt = Jasa::firstOrCreate(['nama_jasa' => 'Ganti V-Belt'], ['harga' => 75000, 'deskripsi' => 'Penggantian V-Belt Motor Matic']);

        // Buat satu pesanan dummy di bulan Mei 2026
        $pesanan = Pesanan::firstOrCreate(
            ['no_pesanan' => 'PES-DUMMY-001'],
            [
                'customer_id' => 1, // Asumsi customer ID 1 ada
                'status' => 'selesai',
                'tanggal_masuk' => Carbon::create(2026, 5, 10, 8, 0, 0),
                'tanggal_selesai' => Carbon::create(2026, 5, 10, 10, 0, 0),
                'deskripsi_pekerjaan' => 'Pesanan Dummy Komisi Mei 2026',
                'created_at' => Carbon::create(2026, 5, 10, 10, 0, 0),
                'updated_at' => Carbon::create(2026, 5, 10, 10, 0, 0),
            ]
        );

        // ============================================
        // 1. Karyawan ASD (Ganti Oli 2x @ 10rb = 20rb)
        // ============================================
        for ($i = 0; $i < 2; $i++) {
            $pjOli = PesananJasa::create([
                'pesanan_id' => $pesanan->id,
                'jasa_id' => $jasaOli->id,
                'harga_snapshot' => $jasaOli->harga,
                'qty' => 1,
                'subtotal' => $jasaOli->harga,
            ]);

            JasaKaryawan::create([
                'pesanan_jasa_id' => $pjOli->id,
                'karyawan_id' => $karyawanAsd->id,
            ]);

            KomisiKaryawan::create([
                'karyawan_id' => $karyawanAsd->id,
                'pesanan_jasa_id' => $pjOli->id,
                'tanggal' => Carbon::create(2026, 5, 10 + $i, 12, 0, 0),
                'nominal_komisi' => 10000,
            ]);
        }

        // ============================================
        // 2. Karyawan ABC (Servis 1x @ 25rb + V-Belt 1x @ 15rb = 40rb)
        // ============================================
        $pjServis = PesananJasa::create([
            'pesanan_id' => $pesanan->id,
            'jasa_id' => $jasaServis->id,
            'harga_snapshot' => $jasaServis->harga,
            'qty' => 1,
            'subtotal' => $jasaServis->harga,
        ]);

        JasaKaryawan::create([
            'pesanan_jasa_id' => $pjServis->id,
            'karyawan_id' => $karyawanAbc->id,
        ]);

        KomisiKaryawan::create([
            'karyawan_id' => $karyawanAbc->id,
            'pesanan_jasa_id' => $pjServis->id,
            'tanggal' => Carbon::create(2026, 5, 15, 14, 0, 0),
            'nominal_komisi' => 25000,
        ]);

        $pjVBelt = PesananJasa::create([
            'pesanan_id' => $pesanan->id,
            'jasa_id' => $jasaVBelt->id,
            'harga_snapshot' => $jasaVBelt->harga,
            'qty' => 1,
            'subtotal' => $jasaVBelt->harga,
        ]);

        JasaKaryawan::create([
            'pesanan_jasa_id' => $pjVBelt->id,
            'karyawan_id' => $karyawanAbc->id,
        ]);

        KomisiKaryawan::create([
            'karyawan_id' => $karyawanAbc->id,
            'pesanan_jasa_id' => $pjVBelt->id,
            'tanggal' => Carbon::create(2026, 5, 16, 10, 0, 0),
            'nominal_komisi' => 15000,
        ]);

        echo "Data Komisi Karyawan berhasil diinjeksi!\n";
    }
}
