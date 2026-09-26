@extends('layouts.app')
@section('title', 'Pengaturan Template Nota')
@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3>Pengaturan Cetak Nota</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pengaturan_nota.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Bengkel <span class="required">*</span></label>
                <input type="text" name="nama_bengkel" class="form-input" required value="{{ old('nama_bengkel', $settings['nama_bengkel']) }}">
            </div>
            <div class="form-group">
                <label class="form-label">No. Telepon / HP</label>
                <input type="text" name="no_telp_bengkel" class="form-input" value="{{ old('no_telp_bengkel', $settings['no_telp_bengkel']) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Bengkel</label>
                <textarea name="alamat_bengkel" class="form-textarea" rows="2">{{ old('alamat_bengkel', $settings['alamat_bengkel']) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan Kaki (Footer)</label>
                <textarea name="catatan_kaki" class="form-textarea" rows="3">{{ old('catatan_kaki', $settings['catatan_kaki']) }}</textarea>
                <small class="text-muted">Gunakan baris baru (Enter) untuk tulisan bersusun.</small>
            </div>
            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
