@extends('layouts.app')
@section('title', 'Detail Logistik')
@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Detail Logistik: {{ $logistik->nama }}</h3>
        <a href="{{ route('admin.logistik.index') }}" class="btn btn-outline">Kembali</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="text-sm text-muted">Nama Barang</div>
                <div class="font-medium text-lg">{{ $logistik->nama }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Satuan</div>
                <div class="font-medium">{{ $logistik->satuan }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Harga Beli</div>
                <div class="font-medium">Rp {{ number_format($logistik->harga_beli, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Stok</div>
                <div class="font-medium {{ $logistik->stok <= $logistik->stok_minimum ? 'text-danger' : '' }}">
                    {{ $logistik->stok }}
                    @if($logistik->stok <= $logistik->stok_minimum)
                        <span class="text-sm">(Stok Rendah!)</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-sm text-muted">Stok Minimum</div>
                <div class="font-medium">{{ $logistik->stok_minimum }}</div>
            </div>
        </div>

        {{-- Restock Form --}}
        <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Restock Logistik</h4>
        <form action="{{ route('admin.logistik.restock', $logistik->id) }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jumlah <span class="required">*</span></label>
                    <input type="number" name="jumlah" class="form-input" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Beli <span class="required">*</span></label>
                    <input type="number" name="harga_beli" class="form-input" value="{{ $logistik->harga_beli }}" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-textarea" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Proses restock?')">Restock</button>
        </form>

        <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--neutral-200);">
            <a href="{{ route('admin.logistik.edit', $logistik->id) }}" class="btn btn-primary">Edit Logistik</a>
        </div>
    </div>
</div>
@endsection
