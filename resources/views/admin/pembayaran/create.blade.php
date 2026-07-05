@extends('layouts.app')
@section('title', 'Buat Pembayaran')
@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3>Form Pembayaran</h3>
        <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-outline">Kembali</a>
    </div>
    <div class="card-body">
        {{-- Pesanan Info --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; padding: 1rem; background: var(--neutral-50); border-radius: 8px;">
            <div>
                <div class="text-sm text-muted">No Pesanan</div>
                <div class="font-medium">{{ $pesanan->no_pesanan }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Customer</div>
                <div class="font-medium">{{ $pesanan->customer->nama ?? '-' }}</div>
            </div>
        </div>

        {{-- Daftar Jasa --}}
        <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Daftar Jasa</h4>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Jasa</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->pesananJasa as $pj)
                    <tr>
                        <td class="font-medium">{{ $pj->jasa->nama_jasa ?? '-' }}</td>
                        <td>Rp {{ number_format($pj->harga_snapshot, 0, ',', '.') }}</td>
                        <td>{{ $pj->qty }}</td>
                        <td>Rp {{ number_format($pj->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: 600;">Total Harga:</td>
                        <td style="font-weight: 600;">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Form Pembayaran --}}
        <form action="{{ route('admin.pembayaran.store') }}" method="POST" style="margin-top: 2rem;">
            @csrf
            <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">

            @php
                $defaultDiskonNominal = $totalHarga * ($pesanan->diskon_persen / 100);
            @endphp

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Diskon (Rp)</label>
                    <input type="number" name="diskon_nominal" class="form-input" value="{{ old('diskon_nominal', $defaultDiskonNominal) }}" min="0" id="diskonInput">
                    @error('diskon_nominal') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">DP / Uang Muka (Rp)</label>
                    <input type="number" name="dp" class="form-input" value="{{ old('dp', $pesanan->dp) }}" min="0" id="dpInput">
                    @error('dp') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Metode Pembayaran <span class="required">*</span></label>
                <select name="metode_bayar" class="form-select" required>
                    <option value="">-- Pilih Metode --</option>
                    <option value="cash" {{ old('metode_bayar') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="transfer" {{ old('metode_bayar') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="debit" {{ old('metode_bayar') == 'debit' ? 'selected' : '' }}>Debit</option>
                </select>
                @error('metode_bayar') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Summary --}}
            <div style="background: var(--neutral-50); border-radius: 8px; padding: 1rem; margin-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Total Harga</span>
                    <span class="font-medium">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">Diskon</span>
                    <span id="diskonDisplay">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="text-muted">DP</span>
                    <span id="dpDisplay">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; border-top: 1px solid var(--neutral-200); font-weight: 600;">
                    <span>Sisa Bayar</span>
                    <span id="sisaBayarDisplay">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Proses pembayaran ini?')">Proses Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    const totalHarga = {{ $totalHarga }};
    const diskonInput = document.getElementById('diskonInput');
    const dpInput = document.getElementById('dpInput');

    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function updateSummary() {
        const diskon = parseInt(diskonInput.value) || 0;
        const dp = parseInt(dpInput.value) || 0;
        const afterDiskon = totalHarga - diskon;
        const sisaBayar = afterDiskon - dp;

        document.getElementById('diskonDisplay').textContent = formatRupiah(diskon);
        document.getElementById('dpDisplay').textContent = formatRupiah(dp);
        document.getElementById('sisaBayarDisplay').textContent = formatRupiah(Math.max(0, sisaBayar));
    }

    diskonInput.addEventListener('input', updateSummary);
    dpInput.addEventListener('input', updateSummary);

    // Initial calculation
    document.addEventListener('DOMContentLoaded', updateSummary);
</script>
@endsection
