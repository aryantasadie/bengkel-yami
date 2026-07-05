@extends('layouts.app')
@section('title', 'Tambah Jasa')
@section('content')
<div class="card max-w-xl">
    <div class="card-header"><h3>Form Tambah Jasa</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.jasa.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Jasa <span class="required">*</span></label>
                <input type="text" name="nama_jasa" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga <span class="required">*</span></label>
                <input type="number" name="harga" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea"></textarea>
            </div>
            <div class="flex gap-4 mt-6">
                <a href="{{ route('admin.jasa.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Jasa</button>
            </div>
        </form>
    </div>
</div>
@endsection