@extends('layouts.app')
@section('title', 'Edit Sparepart')
@section('content')
<div class="card max-w-xl" style="margin: 0 auto;">
    <div class="card-header"><h3>Form Edit Sparepart</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.sparepart.update', $sparepart->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group col-span-2">
                    <label class="form-label">Nama Sparepart <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input" value="{{ $sparepart->nama }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan <span class="required">*</span></label>
                    <input type="text" name="satuan" class="form-input" value="{{ $sparepart->satuan }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Saat Ini</label>
                    <input type="number" name="stok" class="form-input" value="{{ $sparepart->stok }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Beli <span class="required">*</span></label>
                    <input type="number" name="harga_beli" class="form-input" value="{{ $sparepart->harga_beli }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual <span class="required">*</span></label>
                    <input type="number" name="harga_jual" class="form-input" value="{{ $sparepart->harga_jual }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-input" value="{{ $sparepart->stok_minimum }}">
                </div>
            </div>
            <div class="flex gap-4 mt-6">
                <a href="{{ route('admin.sparepart.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Update Sparepart</button>
            </div>
        </form>
    </div>
</div>
@endsection