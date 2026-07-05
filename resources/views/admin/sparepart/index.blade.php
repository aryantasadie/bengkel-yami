@extends('layouts.app')
@section('title', 'Data Sparepart')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Sparepart</h3>
        <a href="{{ route('admin.sparepart.create') }}" class="btn btn-primary">+ Tambah Sparepart</a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Sparepart</th>
                        <th>Satuan</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spareparts ?? [] as $s)
                    <tr>
                        <td class="font-medium">{{ $s->nama }}</td>
                        <td>{{ $s->satuan }}</td>
                        <td>Rp {{ number_format($s->harga_beli, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($s->harga_jual, 0, ',', '.') }}</td>
                        <td>
                            <span class="{{ $s->stok <= $s->stok_minimum ? 'text-danger font-bold' : '' }}">
                                {{ $s->stok }}
                            </span>
                        </td>
                        <td class="text-center table-actions justify-center">
                            <button type="button" class="text-success bg-transparent border-none cursor-pointer font-medium hover:underline" 
                                onclick="openRestockModal('{{ $s->id }}', '{{ addslashes($s->nama) }}', '{{ $s->harga_beli }}', '{{ $s->harga_jual }}', '{{ $s->satuan }}')">Restock</button> | 
                            <a href="{{ route('admin.sparepart.edit', $s->id) }}" class="text-warning">Edit</a> | 
                            <form action="{{ route('admin.sparepart.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sparepart ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-danger bg-transparent border-none cursor-pointer font-medium hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Tidak ada data sparepart.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($spareparts)) <div class="mt-4">{{ $spareparts->links() }}</div> @endif
    </div>
</div>

<script>
function openRestockModal(id, nama, harga_beli, harga_jual, satuan) {
    Swal.fire({
        title: 'Restock ' + nama,
        html: `
            <form id="restockForm-${id}" action="/admin/sparepart/${id}/restock" method="POST" style="text-align: left;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Jumlah Restock (${satuan}) <span style="color:red">*</span></label>
                    <input type="number" name="jumlah" id="jumlah_${id}" class="form-input" style="width: 100%; box-sizing: border-box;" min="1" required oninput="kalkulasiSatuan('${id}')">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Total Harga Beli (Semua Item) <span style="color:red">*</span></label>
                    <input type="number" name="total_harga_beli" id="total_harga_beli_${id}" class="form-input" style="width: 100%; box-sizing: border-box;" required placeholder="Contoh: 500000" oninput="kalkulasiSatuan('${id}')">
                    <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #4b5563; background: #f3f4f6; padding: 0.5rem; border-radius: 0.375rem;">
                        💡 Harga Modal Satuan: <strong id="harga_satuan_text_${id}">Rp 0</strong>/pcs
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Harga Jual Satuan Baru (Rp) <span style="color:red">*</span></label>
                    <input type="number" name="harga_jual" class="form-input" style="width: 100%; box-sizing: border-box;" value="${harga_jual}" required>
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
            if(!form.jumlah.value || !form.total_harga_beli.value || !form.harga_jual.value) {
                Swal.showValidationMessage('Jumlah, Total Harga Beli, dan Harga Jual wajib diisi!');
                return false;
            }
            form.submit();
        }
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