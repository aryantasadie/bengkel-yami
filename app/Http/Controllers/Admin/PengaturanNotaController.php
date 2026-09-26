<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PengaturanNota;
use Illuminate\Http\Request;

class PengaturanNotaController extends Controller {
    public function edit() {
        $settings = [
            'nama_bengkel' => PengaturanNota::get('nama_bengkel', 'Bengkel Yami'),
            'alamat_bengkel' => PengaturanNota::get('alamat_bengkel', 'Jl. Contoh No. 123, Kota'),
            'no_telp_bengkel' => PengaturanNota::get('no_telp_bengkel', '08123456789'),
            'catatan_kaki' => PengaturanNota::get('catatan_kaki', 'Terima kasih atas kepercayaan Anda\nBarang yang sudah dibeli tidak dapat ditukar'),
        ];
        return view('admin.pengaturan_nota.edit', compact('settings'));
    }
    
    public function update(Request $request) {
        $request->validate([
            'nama_bengkel' => 'required|string|max:255',
            'alamat_bengkel' => 'nullable|string',
            'no_telp_bengkel' => 'nullable|string|max:50',
            'catatan_kaki' => 'nullable|string',
        ]);
        
        PengaturanNota::set('nama_bengkel', $request->nama_bengkel);
        PengaturanNota::set('alamat_bengkel', $request->alamat_bengkel);
        PengaturanNota::set('no_telp_bengkel', $request->no_telp_bengkel);
        PengaturanNota::set('catatan_kaki', $request->catatan_kaki);
        
        return redirect()->back()->with('success', 'Pengaturan nota berhasil disimpan');
    }
}
