@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')
@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3>Form Tambah Pengeluaran</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.pengeluaran.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" class="form-input" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                @error('tanggal') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Kategori <span class="required">*</span></label>
                <select name="kategori" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $kat)) }}
                        </option>
                    @endforeach
                </select>
                @error('kategori') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi <span class="required">*</span></label>
                <input type="text" name="deskripsi" class="form-input" value="{{ old('deskripsi') }}" required>
                @error('deskripsi') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Nominal <span class="required">*</span></label>
                <input type="number" name="nominal" class="form-input" value="{{ old('nominal') }}" min="0" required>
                @error('nominal') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-textarea" rows="3">{{ old('keterangan') }}</textarea>
                @error('keterangan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.pengeluaran.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>
@endsection
