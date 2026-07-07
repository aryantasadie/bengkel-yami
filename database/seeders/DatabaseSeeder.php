<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRole;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================
        // USERS (Production Defaults)
        // =====================================================
        
        // Akun Admin Utama
        User::create([
            'karyawan_id' => null,
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nama' => 'Administrator',
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        // Akun Owner (Pemilik Bengkel)
        User::create([
            'karyawan_id' => null,
            'username' => 'owner',
            'password' => Hash::make('owner123'),
            'nama' => 'Pemilik Bengkel Yami',
            'role' => UserRole::OWNER,
            'is_active' => true,
        ]);
        
        // Akun Karyawan / Mekanik (Contoh)
        $mekanik = \App\Models\Karyawan::create([
            'nama' => 'Mekanik Contoh',
            'tanggal_lahir' => '1995-05-15',
            'alamat' => 'Jl. Bengkel Yami No. 123',
            'tanggal_masuk' => now()->toDateString(),
            'jabatan' => 'Mekanik Senior',
            'gaji_pokok' => 3000000,
            'tunjangan' => 500000,
            'is_active' => true,
        ]);

        User::create([
            'karyawan_id' => $mekanik->id,
            'username' => 'coba',
            'password' => Hash::make('password'),
            'nama' => $mekanik->nama,
            'role' => UserRole::KARYAWAN,
            'is_active' => true,
        ]);
        
        // Catatan: Customer, Jasa, Sparepart, Logistik 
        // harus diinput secara manual melalui sistem setelah login.
    }
}
