@extends('layouts.app')
@section('title', 'Data Logistik')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Logistik</h3>
        <a href="{{ route('admin.logistik.create') }}" class="btn btn-primary">+ Tambah Logistik</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="GET" class="filter-bar">
            <div class="search-input-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..." class="form-input">
            </div>
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}>
                Stok Rendah
            </label>
            <button type="submit" class="btn btn-outline">Cari</button>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Harga Beli</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logistiks as $item)
                    <tr>
                        <td class="font-medium">{{ $item->nama }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="{{ $item->stok <= $item->stok_minimum ? 'text-danger' : '' }}">
                                    {{ $item->stok }}
                                </span>
                                @if($item->stok > 0)
                            <button type="button" onclick="decreaseLogistik({{ $item->id }}, this)" class="btn btn-danger" style="padding: 0.1rem 0.4rem; font-size: 0.9rem; line-height: 1; border-radius: 4px;" title="Catat Pemakaian 1 Item">
                                <i class="fas fa-minus">-</i>
                            </button>
                            @endif
                                @if($item->stok <= $item->stok_minimum)
                                    <span class="text-xs text-danger" style="margin-left: 0.5rem;">(min: {{ $item->stok_minimum }})</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center table-actions" style="justify-content: center;">
                            <button type="button" class="text-success bg-transparent border-none cursor-pointer font-medium hover:underline" 
                                onclick="openRestockModal('{{ $item->id }}', '{{ addslashes($item->nama) }}', '{{ $item->harga_beli }}', '{{ $item->satuan }}')">Restock</button> | 
                            <a href="{{ route('admin.logistik.edit', $item->id) }}" class="text-warning">Edit</a> |
                            <a href="#" class="text-danger" onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-form-{{ $item->id }}').submit();">Hapus</a>
                            <form id="delete-form-{{ $item->id }}" action="{{ route('admin.logistik.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data logistik.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $logistiks->links() }}</div>
    </div>
</div>

<script>
function openRestockModal(id, nama, harga_beli, satuan) {
    Swal.fire({
        title: 'Restock ' + nama,
        html: `
            <form id="restockForm-${id}" action="/admin/logistik/${id}/restock" method="POST" style="text-align: left;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Jumlah Restock (${satuan}) <span style="color:red">*</span></label>
                    <input type="number" name="jumlah" id="jumlah_${id}" class="form-input" style="width: 100%; box-sizing: border-box;" min="1" required oninput="kalkulasiSatuan('${id}')">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Total Harga Beli (Semua Item) <span style="color:red">*</span></label>
                    <input type="number" name="total_harga_beli" id="total_harga_beli_${id}" class="form-input" style="width: 100%; box-sizing: border-box;" required placeholder="Contoh: 150000" oninput="kalkulasiSatuan('${id}')">
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #4b5563; background: #f3f4f6; padding: 0.5rem; border-radius: 0.375rem;">
                        💡 Harga Modal Satuan: <strong id="harga_satuan_text_${id}">Rp 0</strong>/pcs
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" class="form-input" style="width: 100%; box-sizing: border-box;" placeholder="Contoh: Pembelian dari supplier X">
                </div>
            </form>
        `,
        showCancelButton: true,
        showConfirmButton: true,
        confirmButtonText: 'Simpan Restock',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#0d9488',
        preConfirm: () => {
            const form = document.getElementById(`restockForm-${id}`);
            if(!form.jumlah.value || !form.total_harga_beli.value) {
                Swal.showValidationMessage('Jumlah dan Total Harga Beli wajib diisi!');
                return false;
            }
            form.submit();
        }
    });
}

function decreaseLogistik(id, btnElement) {
    btnElement.disabled = true; // prevent double click
    
    fetch(`/admin/logistik/${id}/decrease`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the span text containing the stock
            const tdDiv = btnElement.closest('div');
            const stokSpan = tdDiv.querySelector('span:first-child');
            stokSpan.textContent = data.new_stok;
            
            // If stock reaches 0, hide the button
            if (data.new_stok <= 0) {
                btnElement.style.display = 'none';
            }
        } else {
            alert('Error: ' + (data.message || 'Gagal mengurangi stok'));
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan koneksi.');
        console.error(error);
    })
    .finally(() => {
        btnElement.disabled = false;
    });
}

function kalkulasiSatuan(id) {
    let jumlah = parseFloat(document.getElementById(`jumlah_${id}`).value) || 0;
    let total = parseFloat(document.getElementById(`total_harga_beli_${id}`).value) || 0;
    let textElement = document.getElementById(`harga_satuan_text_${id}`);
    
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
