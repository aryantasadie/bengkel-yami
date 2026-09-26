@extends('layouts.app')
@section('title', 'Edit Pesanan')
@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3>Edit Tambahan Sparepart: {{ $pesanan->no_pesanan }}</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.pesanan.update', $pesanan->id) }}" method="POST" id="pesananForm">
            @csrf
            @method('PUT')
            
            <div class="detail-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; background: var(--neutral-50); padding: 1rem; border-radius: 8px;">
                <div>
                    <div class="text-sm text-muted">Customer</div>
                    <div class="font-medium">{{ $pesanan->customer->nama ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-sm text-muted">Tanggal Masuk</div>
                    <div class="font-medium">{{ $pesanan->tanggal_masuk ? $pesanan->tanggal_masuk->format('d M Y, H:i') : '-' }}</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Pekerjaan <span class="required">*</span></label>
                <textarea name="deskripsi_pekerjaan" class="form-textarea" rows="2" required>{{ old('deskripsi_pekerjaan', $pesanan->deskripsi_pekerjaan) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="catatan" class="form-textarea" rows="2">{{ old('catatan', $pesanan->catatan) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Diskon Jasa (%)</label>
                    <input type="number" name="diskon_persen" id="diskon_persen" class="form-input" min="0" max="100" step="0.01" value="{{ old('diskon_persen', $pesanan->diskon_persen) }}" onchange="calculateEstimasi()">
                </div>
                <div class="form-group">
                    <label class="form-label">Diskon Sparepart (%)</label>
                    <input type="number" name="diskon_sparepart_persen" id="diskon_sparepart_persen" class="form-input" min="0" max="100" step="0.01" value="{{ old('diskon_sparepart_persen', $pesanan->diskon_sparepart_persen) }}" onchange="calculateEstimasi()">
                </div>
                <div class="form-group">
                    <label class="form-label">Uang Muka / DP (Rp)</label>
                    <input type="number" name="dp" id="dp" class="form-input" min="0" step="1" value="{{ old('dp', $pesanan->dp) }}" onchange="calculateEstimasi()">
                </div>
            </div>

            {{-- Dynamic Jasa Selection --}}
            <div style="margin-top: 2rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">
                <h4>Layanan Jasa</h4>
            </div>

            <div id="jasa-container">
                @php
                    $jasaList = old('jasas', $pesanan->pesananJasa->map(function($pj) {
                        return [
                            'jasa_id' => $pj->jasa_id, 
                            'karyawan_id' => $pj->jasaKaryawan->first()->karyawan_id ?? null,
                            'nama_custom' => $pj->nama_snapshot,
                            'harga_custom' => $pj->harga_snapshot
                        ];
                    })->toArray());
                @endphp

                @foreach($jasaList as $index => $pj)
                <div class="jasa-row" style="border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <h5 style="margin-top: 0; margin-bottom: 1rem; border-bottom: 1px dashed var(--neutral-200); padding-bottom: 0.5rem;" class="jasa-number-label">Jasa #{{ $index + 1 }}</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Pilih Jasa <span class="required">*</span></label>
                            <select name="jasas[{{ $index }}][jasa_id]" class="form-select select2-jasa" required>
                                <option value="">-- Pilih Jasa --</option>
                                @foreach($jasas as $jasa)
                                    <option value="{{ $jasa->id }}" data-harga="{{ $jasa->harga }}" {{ ($pj['jasa_id'] ?? '') == $jasa->id ? 'selected' : '' }}>
                                        {{ $jasa->nama_jasa }} - Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Karyawan (Opsional)</label>
                            <select name="jasas[{{ $index }}][karyawan_id]" class="form-select select2-karyawan">
                                <option value="">-- Kosong / Pilih Karyawan --</option>
                                @php $currentKaryawanId = $pj['karyawan_id'] ?? null; @endphp
                                @foreach($karyawans as $karyawan)
                                    @if($karyawan->active_tasks_count >= 5 && $karyawan->id != $currentKaryawanId)
                                        <option value="{{ $karyawan->id }}" disabled>
                                            {{ $karyawan->nama }} (Beban Penuh: {{ $karyawan->active_tasks_count }} Tugas)
                                        </option>
                                    @else
                                        <option value="{{ $karyawan->id }}" {{ $karyawan->id == $currentKaryawanId ? 'selected' : '' }}>
                                            {{ $karyawan->nama }} ({{ $karyawan->active_tasks_count }} Tugas Aktif)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row" style="margin-top: 0.5rem;">
                        <div class="form-group">
                            <label class="form-label">Nama Custom (Opsional)</label>
                            <input type="text" name="jasas[{{ $index }}][nama_custom]" class="form-input" placeholder="Ganti nama jasa di nota..." value="{{ $pj['nama_custom'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Custom (Opsional)</label>
                            <input type="number" name="jasas[{{ $index }}][harga_custom]" class="form-input" placeholder="Isi untuk menimpa harga..." value="{{ $pj['harga_custom'] ?? '' }}">
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-jasa" onclick="removeJasa(this)">Hapus Jasa</button>
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
                    $sparepartList = old('spareparts', $pesanan->pesananSparepart->map(function($ps) {
                        return [
                            'sparepart_id' => $ps->sparepart_id, 
                            'qty' => $ps->qty,
                            'nama_custom' => $ps->nama_snapshot,
                            'harga_custom' => $ps->harga_snapshot
                        ];
                    })->toArray() ?? []);
                @endphp

                @foreach($sparepartList as $index => $ps)
                <div class="sparepart-row" style="border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <h5 style="margin-top: 0; margin-bottom: 1rem; border-bottom: 1px dashed var(--neutral-200); padding-bottom: 0.5rem;" class="sparepart-number-label">Sparepart #{{ $index + 1 }}</h5>
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label class="form-label">Pilih Sparepart</label>
                            <select name="spareparts[{{ $index }}][sparepart_id]" class="form-select select2-sparepart" required>
                                <option value="">-- Pilih Sparepart --</option>
                                @foreach($spareparts as $sp)
                                    <option value="{{ $sp->id }}" data-harga="{{ $sp->harga_jual }}" {{ $sp->id == ($ps['sparepart_id'] ?? '') ? 'selected' : '' }}>
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
                    <div class="form-row" style="margin-top: 0.5rem;">
                        <div class="form-group">
                            <label class="form-label">Nama Custom (Opsional)</label>
                            <input type="text" name="spareparts[{{ $index }}][nama_custom]" class="form-input" placeholder="Ganti nama barang di nota..." value="{{ $ps['nama_custom'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Custom (Opsional)</label>
                            <input type="number" name="spareparts[{{ $index }}][harga_custom]" class="form-input" placeholder="Isi untuk menimpa harga..." value="{{ $ps['harga_custom'] ?? '' }}">
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
                    <span class="text-muted">Total Jasa:</span>
                    <span class="font-medium" id="estimasi_total_jasa">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Diskon Jasa (<span id="estimasi_persen_jasa">0</span>%):</span>
                    <span class="font-medium text-danger" id="estimasi_diskon_jasa">- Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Total Sparepart:</span>
                    <span class="font-medium" id="estimasi_total_sparepart">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Diskon Sparepart (<span id="estimasi_persen_sparepart">0</span>%):</span>
                    <span class="font-medium text-danger" id="estimasi_diskon_sparepart">- Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--neutral-200);">
                    <span class="font-bold">Subtotal Keseluruhan:</span>
                    <span class="font-bold" id="estimasi_subtotal">Rp 0</span>
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
                <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
let jasaIndex = {{ $pesanan->pesananJasa->count() }};

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
            <h5 style="margin-top: 0; margin-bottom: 1rem; border-bottom: 1px dashed var(--neutral-200); padding-bottom: 0.5rem;" class="jasa-number-label">Jasa #</h5>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Pilih Jasa <span class="required">*</span></label>
                    <select name="jasas[${jasaIndex}][jasa_id]" class="form-select select2-jasa" required>
                        <option value="">-- Pilih Jasa --</option>
                        ${jasaOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Karyawan (Opsional)</label>
                    <select name="jasas[${jasaIndex}][karyawan_id]" class="form-select select2-karyawan">
                        <option value="">-- Kosong / Pilih Karyawan --</option>
                        ${karyawanOptions}
                    </select>
                </div>
            </div>
            <div class="form-row" style="margin-top: 0.5rem;">
                <div class="form-group">
                    <label class="form-label">Nama Custom (Opsional)</label>
                    <input type="text" name="jasas[${jasaIndex}][nama_custom]" class="form-input" placeholder="Ganti nama jasa di nota...">
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Custom (Opsional)</label>
                    <input type="number" name="jasas[${jasaIndex}][harga_custom]" class="form-input" placeholder="Isi untuk menimpa harga...">
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm remove-jasa" onclick="removeJasa(this)">Hapus Jasa</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    jasaIndex++;
    updateRemoveButtons();
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
        initSelect2();
    }
}

function removeJasa(btn) {
    btn.closest('.jasa-row').remove();
    updateRemoveButtons();
    calculateEstimasi();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.jasa-row');
    rows.forEach((row, i) => {
        const lbl = row.querySelector('.jasa-number-label');
        if (lbl) {
            lbl.innerText = 'Jasa #' + (i + 1);
        }
        const btn = row.querySelector('.remove-jasa');
        if (btn) {
            btn.style.display = rows.length > 1 ? 'inline-flex' : 'none';
        }
    });
}

// Inisialisasi saat load
document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
});

let sparepartIndex = {{ $pesanan->pesananSparepart->count() }};
function addSparepart() {
    const container = document.getElementById('sparepart-container');
    const sparepartOptions = `@foreach($spareparts as $sp)<option value="{{ $sp->id }}" data-harga="{{ $sp->harga_jual }}">{{ $sp->nama }} - Rp {{ number_format($sp->harga_jual, 0, ',', '.') }} (Stok: {{ $sp->stok }})</option>@endforeach`;

    const html = `
        <div class="sparepart-row" style="border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem; border-bottom: 1px dashed var(--neutral-200); padding-bottom: 0.5rem;" class="sparepart-number-label">Sparepart #</h5>
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label class="form-label">Pilih Sparepart</label>
                    <select name="spareparts[${sparepartIndex}][sparepart_id]" class="form-select select2-sparepart" required>
                        <option value="">-- Pilih Sparepart --</option>
                        ${sparepartOptions}
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Jumlah (Qty)</label>
                    <input type="number" name="spareparts[${sparepartIndex}][qty]" class="form-input" min="1" value="1" required>
                </div>
            </div>
            <div class="form-row" style="margin-top: 0.5rem;">
                <div class="form-group">
                    <label class="form-label">Nama Custom (Opsional)</label>
                    <input type="text" name="spareparts[${sparepartIndex}][nama_custom]" class="form-input" placeholder="Ganti nama barang di nota...">
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Custom (Opsional)</label>
                    <input type="number" name="spareparts[${sparepartIndex}][harga_custom]" class="form-input" placeholder="Isi untuk menimpa harga...">
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSparepart(this)">Hapus Sparepart</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    sparepartIndex++;
    updateSparepartNumbers();
    calculateEstimasi();
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
        initSelect2();
    }
}

function removeSparepart(btn) {
    btn.closest('.sparepart-row').remove();
    updateSparepartNumbers();
    calculateEstimasi();
}

function updateSparepartNumbers() {
    const rows = document.querySelectorAll('.sparepart-row');
    rows.forEach((row, i) => {
        const lbl = row.querySelector('.sparepart-number-label');
        if (lbl) {
            lbl.innerText = 'Sparepart #' + (i + 1);
        }
    });
}

function calculateEstimasi() {
    let totalJasa = 0;
    let totalSparepart = 0;

    // Hitung Jasa
    document.querySelectorAll('.jasa-row').forEach(row => {
        const select = row.querySelector('.select2-jasa');
        const customHargaInput = row.querySelector('input[name$="[harga_custom]"]');
        if(select && select.selectedIndex > 0) {
            let harga = parseFloat(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
            if (customHargaInput && customHargaInput.value !== '') {
                harga = parseFloat(customHargaInput.value) || 0;
            }
            totalJasa += harga;
        }
    });

    // Hitung Sparepart
    document.querySelectorAll('.sparepart-row').forEach(row => {
        const select = row.querySelector('.select2-sparepart');
        const qtyInput = row.querySelector('input[name$="[qty]"]');
        const customHargaInput = row.querySelector('input[name$="[harga_custom]"]');
        if(select && select.selectedIndex > 0 && qtyInput) {
            let harga = parseFloat(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
            if (customHargaInput && customHargaInput.value !== '') {
                harga = parseFloat(customHargaInput.value) || 0;
            }
            const qty = parseInt(qtyInput.value) || 0;
            totalSparepart += (harga * qty);
        }
    });

    const diskonPersenJasa = parseFloat(document.getElementById('diskon_persen').value) || 0;
    const diskonPersenSparepart = parseFloat(document.getElementById('diskon_sparepart_persen').value) || 0;
    const dp = parseFloat(document.getElementById('dp').value) || 0;

    const nominalDiskonJasa = totalJasa * (diskonPersenJasa / 100);
    const nominalDiskonSparepart = totalSparepart * (diskonPersenSparepart / 100);
    
    const subtotalJasa = totalJasa - nominalDiskonJasa;
    const subtotalSparepart = totalSparepart - nominalDiskonSparepart;
    const subtotalKeseluruhan = subtotalJasa + subtotalSparepart;

    const sisa = subtotalKeseluruhan - dp;

    // Update UI
    document.getElementById('estimasi_total_jasa').innerText = 'Rp ' + totalJasa.toLocaleString('id-ID');
    document.getElementById('estimasi_persen_jasa').innerText = diskonPersenJasa;
    document.getElementById('estimasi_diskon_jasa').innerText = '- Rp ' + nominalDiskonJasa.toLocaleString('id-ID');

    document.getElementById('estimasi_total_sparepart').innerText = 'Rp ' + totalSparepart.toLocaleString('id-ID');
    document.getElementById('estimasi_persen_sparepart').innerText = diskonPersenSparepart;
    document.getElementById('estimasi_diskon_sparepart').innerText = '- Rp ' + nominalDiskonSparepart.toLocaleString('id-ID');

    document.getElementById('estimasi_subtotal').innerText = 'Rp ' + subtotalKeseluruhan.toLocaleString('id-ID');
    document.getElementById('estimasi_dp').innerText = '- Rp ' + dp.toLocaleString('id-ID');
    document.getElementById('estimasi_sisa').innerText = 'Rp ' + sisa.toLocaleString('id-ID');
}

// Add event listeners to trigger calculation on any change inside form
document.getElementById('pesananForm').addEventListener('change', function(e) {
    if (e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT') {
        calculateEstimasi();
    }
});
document.getElementById('pesananForm').addEventListener('keyup', function(e) {
    if (e.target.tagName === 'INPUT') {
        calculateEstimasi();
    }
});

// Initial calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
    updateSparepartNumbers();
    calculateEstimasi();
    
    // Inisialisasi jQuery & Select2
    if (typeof jQuery !== 'undefined') {
        initSelect2();
    } else {
        const script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
        script.onload = function() {
            const select2Script = document.createElement('script');
            select2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            select2Script.onload = initSelect2;
            document.head.appendChild(select2Script);
            
            const select2Css = document.createElement('link');
            select2Css.rel = 'stylesheet';
            select2Css.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
            document.head.appendChild(select2Css);
        };
        document.head.appendChild(script);
    }
});

function initSelect2() {
    $('.select2-jasa, .select2-karyawan, .select2-sparepart').select2({
        width: '100%',
        matcher: function(params, data) {
            if ($.trim(params.term) === '') { return data; }
            if (typeof data.text === 'undefined') { return null; }
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) { return data; }
            return null;
        }
    });
    
    $('select').on('select2:select', function (e) {
        if ($(this).hasClass('select2-jasa') || $(this).hasClass('select2-sparepart')) {
            const row = $(this).closest('.jasa-row, .sparepart-row');
            const dataHarga = $(this).find(':selected').data('harga');
            const rawText = $(this).find(':selected').text().trim();
            // Parse name by removing the " - Rp XX" part if it exists
            const dataNama = rawText.split(' - Rp ')[0];
            
            row.find('input[name$="[nama_custom]"]').val(dataNama);
            row.find('input[name$="[harga_custom]"]').val(dataHarga);
        }
        calculateEstimasi();
    });
}
</script>
<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid var(--neutral-300);
    border-radius: 6px;
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
</style>
@endsection
