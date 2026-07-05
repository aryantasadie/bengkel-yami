@extends('layouts.app')
@section('title', 'Buat Pesanan')
@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3>Form Buat Pesanan Baru</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.pesanan.store') }}" method="POST" id="pesananForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Customer <span class="required">*</span></label>
                <select name="customer_id" id="customer_id" class="form-select" required onchange="updateDiskonFromCustomer()">
                    <option value="" data-diskon="0">-- Pilih Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" data-diskon="{{ $customer->diskon_default }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->nama }} {{ $customer->no_telp ? '('.$customer->no_telp.')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Pekerjaan <span class="required">*</span></label>
                <textarea name="deskripsi_pekerjaan" class="form-textarea" rows="3" required>{{ old('deskripsi_pekerjaan') }}</textarea>
                @error('deskripsi_pekerjaan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-textarea" rows="2">{{ old('catatan') }}</textarea>
                @error('catatan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" name="diskon_persen" id="diskon_persen" class="form-input" min="0" max="100" step="0.01" value="{{ old('diskon_persen', 0) }}" onchange="calculateEstimasi()">
                    <span class="text-xs text-muted">Akan terisi otomatis berdasarkan Customer</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Uang Muka / DP (Rp)</label>
                    <input type="number" name="dp" id="dp" class="form-input" min="0" step="1" value="{{ old('dp', 0) }}" onchange="calculateEstimasi()">
                </div>
            </div>

            {{-- Dynamic Jasa Selection --}}
            <div style="margin-top: 2rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">
                <h4>Layanan Jasa</h4>
            </div>

            <div id="jasa-container">
                @php
                    $oldJasas = old('jasas', [['jasa_id' => '', 'karyawan_id' => '']]);
                @endphp
                @foreach($oldJasas as $index => $pj)
                <div class="jasa-row" style="border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Pilih Jasa <span class="required">*</span></label>
                            <select name="jasas[{{ $index }}][jasa_id]" class="form-select jasa-select" required>
                                <option value="">-- Pilih Jasa --</option>
                                @foreach($jasas as $jasa)
                                    <option value="{{ $jasa->id }}" data-harga="{{ $jasa->harga }}" {{ ($pj['jasa_id'] ?? '') == $jasa->id ? 'selected' : '' }}>
                                        {{ $jasa->nama_jasa }} - Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Karyawan <span class="required">*</span></label>
                            <select name="jasas[{{ $index }}][karyawan_id]" class="form-select" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($karyawans as $karyawan)
                                    @if($karyawan->active_tasks_count >= 5 && $karyawan->id != ($pj['karyawan_id'] ?? null))
                                        <option value="{{ $karyawan->id }}" disabled>
                                            {{ $karyawan->nama }} (Beban Penuh: {{ $karyawan->active_tasks_count }} Tugas)
                                        </option>
                                    @else
                                        <option value="{{ $karyawan->id }}" {{ ($pj['karyawan_id'] ?? '') == $karyawan->id ? 'selected' : '' }}>
                                            {{ $karyawan->nama }} ({{ $karyawan->active_tasks_count }} Tugas Aktif)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-jasa" style="{{ count($oldJasas) > 1 ? 'display:inline-flex;' : 'display:none;' }}" onclick="removeJasa(this)">Hapus Jasa</button>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline" onclick="addJasa()">+ Tambah Jasa</button>

            {{-- Dynamic Sparepart Selection --}}
            <div style="margin-top: 2rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">
                <h4>Layanan Sparepart / Barang (Opsional)</h4>
            </div>

            <div id="sparepart-container">
                @php
                    $oldSpareparts = old('spareparts', []);
                @endphp
                @foreach($oldSpareparts as $index => $ps)
                <div class="sparepart-row" style="border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label class="form-label">Pilih Sparepart</label>
                            <select name="spareparts[{{ $index }}][sparepart_id]" class="form-select sparepart-select" required>
                                <option value="">-- Pilih Sparepart --</option>
                                @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}" data-harga="{{ $sp->harga_jual }}" {{ ($ps['sparepart_id'] ?? '') == $sp->id ? 'selected' : '' }}>
                                        {{ $sp->nama }} - Rp {{ number_format($sp->harga_jual, 0, ',', '.') }} (Stok saat ini: {{ $sp->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label">Jumlah (Qty)</label>
                            <input type="number" name="spareparts[{{ $index }}][qty]" class="form-input" min="1" value="{{ $ps['qty'] ?? 1 }}" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.sparepart-row').remove()">Hapus Sparepart</button>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline" onclick="addSparepart()">+ Tambah Sparepart</button>

            {{-- Ringkasan Estimasi --}}
            <div style="margin-top: 2rem; background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1.5rem;">
                <h4 style="margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.5rem;">Ringkasan Estimasi</h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Subtotal Jasa & Sparepart:</span>
                    <span class="font-medium" id="estimasi_subtotal">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Diskon (<span id="estimasi_persen">0</span>%):</span>
                    <span class="font-medium text-danger" id="estimasi_diskon">- Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Uang Muka (DP):</span>
                    <span class="font-medium text-success" id="estimasi_dp">- Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--neutral-300);">
                    <strong style="font-size: 1.1rem;">Estimasi Sisa Tagihan:</strong>
                    <strong style="font-size: 1.1rem; color: var(--primary-600);" id="estimasi_sisa">Rp 0</strong>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
            </div>
        </form>
    </div>
</div>

<script>
let jasaIndex = 1;

function addJasa() {
    const container = document.getElementById('jasa-container');
    const jasaOptions = `@foreach($jasas as $jasa)<option value="{{ $jasa->id }}" data-harga="{{ $jasa->harga }}">{{ $jasa->nama_jasa }} - Rp {{ number_format($jasa->harga, 0, ',', '.') }}</option>@endforeach`;
    const karyawanOptions = `@foreach($karyawans as $karyawan)
        @if($karyawan->active_tasks_count >= 5)
            <option value="{{ $karyawan->id }}" disabled>{{ $karyawan->nama }} (Beban Penuh: {{ $karyawan->active_tasks_count }} Tugas)</option>
        @else
            <option value="{{ $karyawan->id }}">{{ $karyawan->nama }} ({{ $karyawan->active_tasks_count }} Tugas Aktif)</option>
        @endif
    @endforeach`;

    const html = `
        <div class="jasa-row" style="border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Pilih Jasa <span class="required">*</span></label>
                    <select name="jasas[${jasaIndex}][jasa_id]" class="form-select jasa-select" required>
                        <option value="">-- Pilih Jasa --</option>
                        ${jasaOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Karyawan <span class="required">*</span></label>
                    <select name="jasas[${jasaIndex}][karyawan_id]" class="form-select" required>
                        <option value="">-- Pilih Karyawan --</option>
                        ${karyawanOptions}
                    </select>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm remove-jasa" onclick="removeJasa(this)">Hapus Jasa</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    jasaIndex++;
    updateRemoveButtons();
}

function removeJasa(btn) {
    btn.closest('.jasa-row').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.jasa-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-jasa');
        if (btn) {
            btn.style.display = rows.length > 1 ? 'inline-flex' : 'none';
        }
    });
}

let sparepartIndex = 0;
function addSparepart() {
    const container = document.getElementById('sparepart-container');
    const sparepartOptions = `@foreach($spareparts as $sp)<option value="{{ $sp->id }}" data-harga="{{ $sp->harga_jual }}">{{ $sp->nama }} - Rp {{ number_format($sp->harga_jual, 0, ',', '.') }} (Stok: {{ $sp->stok }})</option>@endforeach`;

    const html = `
        <div class="sparepart-row" style="border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label class="form-label">Pilih Sparepart</label>
                    <select name="spareparts[${sparepartIndex}][sparepart_id]" class="form-select sparepart-select" required>
                        <option value="">-- Pilih Sparepart --</option>
                        ${sparepartOptions}
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Jumlah (Qty)</label>
                    <input type="number" name="spareparts[${sparepartIndex}][qty]" class="form-input" min="1" value="1" required>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.sparepart-row').remove()">Hapus Sparepart</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    sparepartIndex++;
    calculateEstimasi();
}

function updateDiskonFromCustomer() {
    const select = document.getElementById('customer_id');
    if(select.selectedIndex > 0) {
        const diskon = select.options[select.selectedIndex].getAttribute('data-diskon');
        document.getElementById('diskon_persen').value = diskon;
    } else {
        document.getElementById('diskon_persen').value = 0;
    }
    calculateEstimasi();
}

function calculateEstimasi() {
    let subtotal = 0;

    // Hitung Jasa
    document.querySelectorAll('.jasa-select').forEach(select => {
        if(select.selectedIndex > 0) {
            const harga = parseFloat(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
            subtotal += harga;
        }
    });

    // Hitung Sparepart
    document.querySelectorAll('.sparepart-row').forEach(row => {
        const select = row.querySelector('.sparepart-select');
        const qtyInput = row.querySelector('input[type="number"]');
        if(select && select.selectedIndex > 0 && qtyInput) {
            const harga = parseFloat(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            subtotal += (harga * qty);
        }
    });

    const diskonPersen = parseFloat(document.getElementById('diskon_persen').value) || 0;
    const dp = parseFloat(document.getElementById('dp').value) || 0;

    const nominalDiskon = subtotal * (diskonPersen / 100);
    const sisa = subtotal - nominalDiskon - dp;

    // Update UI
    document.getElementById('estimasi_subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('estimasi_persen').innerText = diskonPersen;
    document.getElementById('estimasi_diskon').innerText = '- Rp ' + nominalDiskon.toLocaleString('id-ID');
    document.getElementById('estimasi_dp').innerText = '- Rp ' + dp.toLocaleString('id-ID');
    document.getElementById('estimasi_sisa').innerText = 'Rp ' + Math.max(0, sisa).toLocaleString('id-ID');
}

// Add event listeners to trigger calculation on any change inside form
document.getElementById('pesananForm').addEventListener('change', function(e) {
    if (e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT') {
        calculateEstimasi();
    }
});

// Initial calculation on page load
document.addEventListener('DOMContentLoaded', calculateEstimasi);

</script>
@endsection
