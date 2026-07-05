@extends('layouts.app')
@section('title', 'Detail Sparepart')
@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Detail Sparepart: {{ $sparepart->nama }}</h3>
        <a href="{{ route('admin.sparepart.index') }}" class="btn btn-outline">Kembali</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="text-sm text-muted">Nama Sparepart</div>
                <div class="font-medium text-lg">{{ $sparepart->nama }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Satuan</div>
                <div class="font-medium">{{ $sparepart->satuan }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Harga Beli</div>
                <div class="font-medium">Rp {{ number_format($sparepart->harga_beli, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Harga Jual</div>
                <div class="font-medium">Rp {{ number_format($sparepart->harga_jual, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Stok</div>
                <div class="font-medium {{ $sparepart->stok <= $sparepart->stok_minimum ? 'text-danger' : '' }}">
                    {{ $sparepart->stok }}
                    @if($sparepart->stok <= $sparepart->stok_minimum)
                        <span class="text-sm">(Stok Rendah!)</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-sm text-muted">Stok Minimum</div>
                <div class="font-medium">{{ $sparepart->stok_minimum }}</div>
            </div>
        </div>

        {{-- Restock Form --}}
        <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Restock Sparepart</h4>
        <form action="{{ route('admin.sparepart.restock', $sparepart->id) }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jumlah <span class="required">*</span></label>
                    <input type="number" name="jumlah" id="jumlah" class="form-input" min="1" required oninput="kalkulasiSatuanShow()">
                </div>
                <div class="form-group">
                    <label class="form-label">Total Harga Beli (Semua Item) <span class="required">*</span></label>
                    <input type="number" name="total_harga_beli" id="total_harga_beli" class="form-input" required placeholder="Contoh: 500000" oninput="kalkulasiSatuanShow()">
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #4b5563; background: #f3f4f6; padding: 0.5rem; border-radius: 0.375rem;">
                        💡 Harga Modal Satuan: <strong id="harga_satuan_text">Rp 0</strong>/pcs
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-textarea" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Proses restock?')">Restock</button>
        </form>

        <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--neutral-200);">
            <a href="{{ route('admin.sparepart.edit', $sparepart->id) }}" class="btn btn-primary">Edit Sparepart</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function kalkulasiSatuanShow() {
    let jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
    let total = parseFloat(document.getElementById('total_harga_beli').value) || 0;
    let textElement = document.getElementById('harga_satuan_text');
    
    if (jumlah > 0 && total > 0) {
        let satuan = total / jumlah;
        textElement.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(satuan);
        textElement.style.color = '#059669'; // green
    } else {
        textElement.innerText = 'Rp 0';
        textElement.style.color = '#4b5563';
    }
}
</script>
@endsection
