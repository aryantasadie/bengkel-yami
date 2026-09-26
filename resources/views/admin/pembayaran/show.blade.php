@extends('layouts.app')
@section('title', 'Detail Pembayaran')
@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3>Nota Pembayaran: {{ $transaksi->no_nota }}</h3>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.pembayaran.print', $transaksi->id) }}" class="btn btn-outline" target="_blank">Cetak Nota</a>
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        {{-- Info Transaksi --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="text-sm text-muted">No Nota</div>
                <div class="font-medium">{{ $transaksi->no_nota }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Tanggal</div>
                <div class="font-medium">{{ $transaksi->tanggal ? $transaksi->tanggal->format('d M Y, H:i') : '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">No Pesanan</div>
                <div class="font-medium">{{ $transaksi->pesanan->no_pesanan ?? '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Customer</div>
                <div class="font-medium">{{ $transaksi->pesanan->customer->nama ?? '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Metode Bayar</div>
                <div class="font-medium">{{ ucfirst($transaksi->metode_bayar) }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Status Bayar</div>
                <div>
                    @if($transaksi->status_bayar == 'lunas')
                        <span class="badge badge-selesai">Lunas</span>
                    @elseif($transaksi->status_bayar == 'dp')
                        <span class="badge badge-proses">DP</span>
                    @else
                        <span class="badge badge-antrian">Belum Bayar</span>
                    @endif
                </div>
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
                    @foreach($transaksi->pesanan->pesananJasa as $pj)
                    <tr>
                        <td class="font-medium">{{ $pj->nama_snapshot ?? $pj->jasa->nama_jasa ?? '-' }}</td>
                        <td>Rp {{ number_format($pj->harga_snapshot, 0, ',', '.') }}</td>
                        <td>{{ $pj->qty }}</td>
                        <td>Rp {{ number_format($pj->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Daftar Sparepart --}}
        @if($transaksi->pesanan->pesananSparepart->count() > 0)
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Daftar Sparepart</h4>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Sparepart</th>
                        <th>Harga Jual</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->pesanan->pesananSparepart as $ps)
                    <tr>
                        <td class="font-medium">{{ $ps->nama_snapshot ?? $ps->sparepart->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($ps->harga_snapshot, 0, ',', '.') }}</td>
                        <td>{{ $ps->qty }}</td>
                        <td>Rp {{ number_format($ps->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Ringkasan Pembayaran --}}
        @php
            $pesanan = $transaksi->pesanan;
            $totalJasa = $pesanan->pesananJasa->sum('subtotal');
            $totalSparepart = $pesanan->pesananSparepart->sum('subtotal');
            $nominalDiskonJasa = $totalJasa * ($pesanan->diskon_persen / 100);
            $nominalDiskonSparepart = $totalSparepart * ($pesanan->diskon_sparepart_persen / 100);
        @endphp
        <div style="background: var(--neutral-50); border-radius: 8px; padding: 1.5rem; margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Total Jasa</span>
                <span>Rp {{ number_format($totalJasa, 0, ',', '.') }}</span>
            </div>
            @if($pesanan->diskon_persen > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Diskon Jasa ({{ $pesanan->diskon_persen }}%)</span>
                <span class="text-danger">- Rp {{ number_format($nominalDiskonJasa, 0, ',', '.') }}</span>
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Total Sparepart</span>
                <span>Rp {{ number_format($totalSparepart, 0, ',', '.') }}</span>
            </div>
            @if($pesanan->diskon_sparepart_persen > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Diskon Sparepart ({{ $pesanan->diskon_sparepart_persen }}%)</span>
                <span class="text-danger">- Rp {{ number_format($nominalDiskonSparepart, 0, ',', '.') }}</span>
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--neutral-200);">
                <span class="font-medium">Total Bayar (Subtotal Keseluruhan)</span>
                <span class="font-medium">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
            </div>
            @if($transaksi->dp > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">DP</span>
                <span>Rp {{ number_format($transaksi->dp, 0, ',', '.') }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 1.1rem; padding-top: 0.5rem; border-top: 1px solid var(--neutral-200);">
                <span>Sisa Bayar</span>
                <span class="{{ $transaksi->sisa_bayar > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($transaksi->status_bayar !== 'lunas')
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--neutral-200); text-align: right;">
            <form action="{{ route('admin.pembayaran.pelunasan', $transaksi->id) }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi: Customer telah membayar sisa tagihan sebesar Rp {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}?')">Lunasi Pembayaran</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
