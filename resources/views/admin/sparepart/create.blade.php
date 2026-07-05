@extends('layouts.app')
@section('title', 'Tambah Sparepart')
@section('content')
<div class="card max-w-xl" style="margin: 0 auto;">
    <div class="card-header"><h3>Form Tambah Sparepart</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.sparepart.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group col-span-2">
                    <label class="form-label">Nama Sparepart <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan <span class="required">*</span></label>
                    <input type="text" name="satuan" class="form-input" placeholder="pcs, set, liter" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Awal <span class="required">*</span></label>
                    <input type="number" name="stok" id="stok" class="form-input" value="0" required oninput="kalkulasiSatuanCreate()">
                </div>
                <div class="form-group">
                    <label class="form-label">Total Harga Beli (Berdasarkan Stok) <span class="required">*</span></label>
                    <input type="number" name="total_harga_beli" id="total_harga_beli" class="form-input" required oninput="kalkulasiSatuanCreate()">
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #4b5563;">
                        💡 Harga Modal Satuan: <strong id="harga_satuan_text">Rp 0</strong>/pcs
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual <span class="required">*</span></label>
                    <input type="number" name="harga_jual" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-input" value="5">
                </div>
            </div>
            <div class="flex gap-4 mt-6">
                <a href="{{ route('admin.sparepart.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Sparepart</button>
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