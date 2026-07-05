@extends('layouts.app')
@section('title', 'Edit Logistik')
@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Edit Logistik: {{ $logistik->nama }}</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.logistik.update', $logistik->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Barang <span class="required">*</span></label>
                <input type="text" name="nama" class="form-input" value="{{ old('nama', $logistik->nama) }}" required>
                @error('nama') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Satuan <span class="required">*</span></label>
                <input type="text" name="satuan" class="form-input" value="{{ old('satuan', $logistik->satuan) }}" required>
                @error('satuan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Harga Beli <span class="required">*</span></label>
                <input type="number" name="harga_beli" class="form-input" value="{{ old('harga_beli', $logistik->harga_beli) }}" min="0" required>
                @error('harga_beli') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok <span class="required">*</span></label>
                    <input type="number" name="stok" class="form-input" value="{{ old('stok', $logistik->stok) }}" min="0" required>
                    @error('stok') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-input" value="{{ old('stok_minimum', $logistik->stok_minimum) }}" min="0">
                    @error('stok_minimum') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.logistik.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Update Logistik</button>
            </div>
        </form>
    </div>
</div>
@endsection
