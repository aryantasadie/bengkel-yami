@extends('layouts.app')
@section('title', 'Tambah Logistik')
@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Form Tambah Logistik</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.logistik.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Barang <span class="required">*</span></label>
                <input type="text" name="nama" class="form-input" value="{{ old('nama') }}" required>
                @error('nama') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Satuan <span class="required">*</span></label>
                <input type="text" name="satuan" class="form-input" value="{{ old('satuan') }}" placeholder="pcs, liter, kg, dll" required>
                @error('satuan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Total Harga Beli (Berdasarkan Stok) <span class="required">*</span></label>
                <input type="number" name="total_harga_beli" id="total_harga_beli" class="form-input" value="{{ old('total_harga_beli') }}" min="0" required oninput="kalkulasiSatuanCreate()">
                <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #4b5563;">
                    💡 Harga Modal Satuan: <strong id="harga_satuan_text">Rp 0</strong>/pcs
                </div>
                @error('total_harga_beli') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok <span class="required">*</span></label>
                    <input type="number" name="stok" id="stok" class="form-input" value="{{ old('stok', 0) }}" min="0" required oninput="kalkulasiSatuanCreate()">
                    @error('stok') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-input" value="{{ old('stok_minimum', 0) }}" min="0">
                    @error('stok_minimum') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.logistik.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Logistik</button>
            </div>
        </form>
    </div>
</div>

<script>
function kalkulasiSatuanCreate() {
    let jumlah = parseFloat(document.getElementById('stok').value) || 0;
    let total = parseFloat(document.getElementById('total_harga_beli').value) || 0;
    let textElement = document.getElementById('harga_satuan_text');
    
    if (jumlah > 0 && total > 0) {
        let satuan = total / jumlah;
        textElement.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(satuan);
        textElement.style.color = '#059669'; // green
    } else if (jumlah === 0 && total > 0) {
        textElement.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total) + ' (Tanpa Stok)';
        textElement.style.color = '#ca8a04'; // yellow
    } else {
        textElement.innerText = 'Rp 0';
        textElement.style.color = '#4b5563';
    }
}
</script>
@endsection
