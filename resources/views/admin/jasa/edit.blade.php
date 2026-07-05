@extends('layouts.app')
@section('title', 'Edit Jasa')
@section('content')
<div class="card max-w-xl">
    <div class="card-header"><h3>Form Edit Jasa</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.jasa.update', $jasa->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Jasa <span class="required">*</span></label>
                <input type="text" name="nama_jasa" class="form-input" value="{{ $jasa->nama_jasa }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga <span class="required">*</span></label>
                <input type="number" name="harga" class="form-input" value="{{ $jasa->harga }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-textarea">{{ $jasa->deskripsi }}</textarea>
            </div>
            <div class="flex gap-4 mt-6">
                <a href="{{ route('admin.jasa.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Update Jasa</button>
            </div>
        </form>
    </div>
</div>
@endsection