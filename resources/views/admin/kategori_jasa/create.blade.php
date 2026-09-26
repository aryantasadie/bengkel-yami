@extends('layouts.app')
@section('title', 'Tambah Kategori Jasa')
@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3>Tambah Kategori Jasa</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.kategori_jasa.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori <span class="required">*</span></label>
                <input type="text" name="nama_kategori" class="form-input" required value="{{ old('nama_kategori') }}">
                @error('nama_kategori') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.kategori_jasa.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
