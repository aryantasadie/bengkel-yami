@extends('layouts.app')
@section('title', 'Tambah Customer')
@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3>Form Tambah Customer</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Customer <span class="required">*</span></label>
                <input type="text" name="nama" class="form-input" value="{{ old('nama') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">No Telepon</label>
                <input type="text" name="no_telp" class="form-input" value="{{ old('no_telp') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-textarea">{{ old('alamat') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Diskon Default (%)</label>
                <input type="number" step="0.01" name="diskon_default" class="form-input" value="{{ old('diskon_default', '0') }}">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection