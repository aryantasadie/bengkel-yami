@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
<div class="card" style="max-width: 900px;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3>Detail Pesanan: {{ $pesanan->no_pesanan }}</h3>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline">Kembali</a>
            <a href="{{ route('admin.pesanan.print', ['id' => $pesanan->id, 'with_diskon' => 0]) }}" class="btn btn-outline" target="_blank">Cetak Estimasi (Normal)</a>
            <a href="{{ route('admin.pesanan.print', ['id' => $pesanan->id, 'with_diskon' => 1]) }}" class="btn btn-outline" target="_blank">Cetak Estimasi (Diskon)</a>
            @if(in_array($pesanan->status, ['antrian', 'proses']))
                <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}" class="btn btn-primary">Edit Sparepart</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Pesanan Info --}}
        <div class="detail-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="detail-item-label text-sm text-muted">No Pesanan</div>
                <div class="detail-item-value font-medium">{{ $pesanan->no_pesanan }}</div>
            </div>
            <div>
                <div class="detail-item-label text-sm text-muted">Status</div>
                <div class="detail-item-value">
                    @switch($pesanan->status)
                        @case('antrian')
                            <span class="badge badge-antrian">Antrian</span>
                            @break
                        @case('proses')
                            <span class="badge badge-proses">Proses</span>
                            @break
                        @case('selesai')
                            <span class="badge badge-selesai">Selesai</span>
                            @break
                        @case('dibatalkan')
                            <span class="badge badge-dibatalkan">Dibatalkan</span>
                            @break
                        @default
                            <span class="badge">{{ ucfirst($pesanan->status) }}</span>
                    @endswitch
                </div>
            </div>
            <div>
                <div class="detail-item-label text-sm text-muted">Customer</div>
                <div class="detail-item-value font-medium">{{ $pesanan->customer->nama ?? '-' }}</div>
                @if($pesanan->customer && $pesanan->customer->no_telp)
                    <div class="text-sm text-muted">{{ $pesanan->customer->no_telp }}</div>
                @endif
            </div>
            <div>
                <div class="detail-item-label text-sm text-muted">Tanggal Masuk</div>
                <div class="detail-item-value">{{ $pesanan->tanggal_masuk ? $pesanan->tanggal_masuk->format('d M Y, H:i') : '-' }}</div>
            </div>
            @if($pesanan->tanggal_selesai)
            <div>
                <div class="detail-item-label text-sm text-muted">Tanggal Selesai</div>
                <div class="detail-item-value">{{ $pesanan->tanggal_selesai->format('d M Y, H:i') }}</div>
            </div>
            @endif
            @if($pesanan->deskripsi_pekerjaan)
            <div style="grid-column: 1 / -1;">
                <div class="detail-item-label text-sm text-muted">Deskripsi Pekerjaan</div>
                <div class="detail-item-value">{{ $pesanan->deskripsi_pekerjaan }}</div>
            </div>
            @endif
            @if($pesanan->catatan)
            <div style="grid-column: 1 / -1;">
                <div class="detail-item-label text-sm text-muted">Catatan</div>
                <div class="detail-item-value">{{ $pesanan->catatan }}</div>
            </div>
            @endif
        </div>

        {{-- Jasa Items --}}
        <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Daftar Jasa</h4>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Jasa</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Karyawan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalJasa = 0; @endphp
                    @forelse($pesanan->pesananJasa as $pj)
                    @php $totalJasa += $pj->subtotal; @endphp
                    <tr>
                        <td class="font-medium">{{ $pj->nama_snapshot ?? $pj->jasa->nama_jasa ?? '-' }}</td>
                        <td>Rp {{ number_format($pj->harga_snapshot, 0, ',', '.') }}</td>
                        <td>{{ $pj->qty }}</td>
                        <td>Rp {{ number_format($pj->subtotal, 0, ',', '.') }}</td>
                        <td>
                            @forelse($pj->jasaKaryawan as $jk)
                                @php
                                    $statusColor = 'var(--neutral-400)';
                                    if($jk->status == 'proses') $statusColor = 'var(--warning)';
                                    if($jk->status == 'selesai') $statusColor = 'var(--success)';
                                @endphp
                                <div style="margin-bottom: 4px; display: flex; align-items: center; gap: 0.5rem;">
                                    <span class="badge badge-aktif" style="border-left: 3px solid {{ $statusColor }};">
                                        {{ $jk->karyawan->nama ?? '-' }} 
                                    </span>
                                    @if(!$pesanan->transaksi && $pesanan->status !== 'dibatalkan')
                                        <form action="{{ route('admin.pesanan.updateJasaKaryawanStatus', $jk->id) }}" method="POST" style="margin: 0;">
                                            @csrf @method('PUT')
                                            @php
                                                $selectBg = '#f3f4f6';
                                                $selectColor = '#374151';
                                                $selectBorder = '#d1d5db';
                                                
                                                if($jk->status == 'proses') {
                                                    $selectBg = '#fef3c7';
                                                    $selectColor = '#92400e';
                                                    $selectBorder = '#fcd34d';
                                                } elseif($jk->status == 'selesai') {
                                                    $selectBg = '#dcfce3';
                                                    $selectColor = '#166534';
                                                    $selectBorder = '#86efac';
                                                }
                                            @endphp
                                            <select name="status" onchange="this.form.submit()" style="padding: 4px 24px 4px 10px; font-size: 0.75rem; font-weight: 700; color: {{ $selectColor }}; background-color: {{ $selectBg }}; border: 1px solid {{ $selectBorder }}; border-radius: 9999px; outline: none; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23{{ ltrim($selectColor, '#') }}%22%20stroke-width%3D%223%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 6px center; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05); text-transform: uppercase; letter-spacing: 0.5px;">
                                                <option value="ditugaskan" {{ $jk->status == 'ditugaskan' ? 'selected' : '' }} style="background: white; color: #374151;">Ditugaskan</option>
                                                <option value="proses" {{ $jk->status == 'proses' ? 'selected' : '' }} style="background: white; color: #374151;">Proses</option>
                                                <option value="selesai" {{ $jk->status == 'selesai' ? 'selected' : '' }} style="background: white; color: #374151;">Selesai</option>
                                            </select>
                                        </form>
                                    @else
                                        <small style="opacity: 0.8; font-weight: normal;">({{ ucfirst($jk->status) }})</small>
                                    @endif
                                </div>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data jasa.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: 600;">Total:</td>
                        <td colspan="2" style="font-weight: 600;">Rp {{ number_format($totalJasa, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Sparepart Items --}}
        @if($pesanan->pesananSparepart->count() > 0)
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Daftar Sparepart Tambahan</h4>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Sparepart</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSparepart = 0; @endphp
                    @foreach($pesanan->pesananSparepart as $ps)
                    @php $totalSparepart += $ps->subtotal; @endphp
                    <tr>
                        <td class="font-medium">{{ $ps->nama_snapshot ?? $ps->sparepart->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($ps->harga_snapshot, 0, ',', '.') }}</td>
                        <td>{{ $ps->qty }}</td>
                        <td>Rp {{ number_format($ps->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: 600;">Total Sparepart:</td>
                        <td style="font-weight: 600;">Rp {{ number_format($totalSparepart, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        {{-- Ringkasan Tagihan --}}
        @php
            $nominalDiskonJasa = $totalJasa * ($pesanan->diskon_persen / 100);
            $nominalDiskonSparepart = ($totalSparepart ?? 0) * ($pesanan->diskon_sparepart_persen / 100);
            $subtotalJasa = $totalJasa - $nominalDiskonJasa;
            $subtotalSparepart = ($totalSparepart ?? 0) - $nominalDiskonSparepart;
            $subtotalSemua = $subtotalJasa + $subtotalSparepart;
            $sisaTagihan = $subtotalSemua - $pesanan->dp;
        @endphp
        <div style="margin-top: 2rem; background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: 8px; padding: 1.5rem; max-width: 500px; margin-left: auto;">
            <h4 style="margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.5rem; text-align: right;">Ringkasan Tagihan</h4>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Total Jasa:</span>
                <span class="font-medium">Rp {{ number_format($totalJasa, 0, ',', '.') }}</span>
            </div>
            @if($pesanan->diskon_persen > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Diskon Jasa ({{ $pesanan->diskon_persen }}%):</span>
                <span class="font-medium text-danger">- Rp {{ number_format($nominalDiskonJasa, 0, ',', '.') }}</span>
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Total Sparepart:</span>
                <span class="font-medium">Rp {{ number_format($totalSparepart ?? 0, 0, ',', '.') }}</span>
            </div>
            @if($pesanan->diskon_sparepart_persen > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Diskon Sparepart ({{ $pesanan->diskon_sparepart_persen }}%):</span>
                <span class="font-medium text-danger">- Rp {{ number_format($nominalDiskonSparepart, 0, ',', '.') }}</span>
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--neutral-200);">
                <span class="font-bold">Subtotal Keseluruhan:</span>
                <span class="font-bold">Rp {{ number_format($subtotalSemua, 0, ',', '.') }}</span>
            </div>

            @if($pesanan->dp > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-muted">Uang Muka (DP):</span>
                <span class="font-medium text-success">- Rp {{ number_format($pesanan->dp, 0, ',', '.') }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--neutral-300);">
                <strong style="font-size: 1.1rem;">Estimasi Sisa Tagihan:</strong>
                <strong style="font-size: 1.1rem; color: var(--primary-600);">Rp {{ number_format(max(0, $sisaTagihan), 0, ',', '.') }}</strong>
            </div>
        </div>

        {{-- Status Update Buttons --}}
        @if(!$pesanan->transaksi && in_array($pesanan->status, ['antrian', 'proses']))
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--neutral-200);">
            <h4 style="margin-bottom: 1rem;">Update Status</h4>
            <div style="display: flex; gap: 0.75rem;">
                @if($pesanan->status == 'antrian')
                    <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="proses">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Ubah status menjadi Proses?')">Mulai Proses</button>
                    </form>
                @endif
                
                <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" class="btn btn-primary" style="background: var(--success);" onclick="return confirm('Tandai pesanan sebagai Selesai?')">Tandai Selesai</button>
                </form>
                <form action="{{ route('admin.pesanan.updateStatus', $pesanan->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="dibatalkan">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">Batalkan</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Link to Pembayaran --}}
        @if($pesanan->status == 'selesai' && !$pesanan->transaksi)
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--neutral-200);">
            <a href="{{ route('admin.pembayaran.create', $pesanan->id) }}" class="btn btn-primary">Buat Pembayaran</a>
        </div>
        @endif

        @if($pesanan->transaksi)
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--neutral-200);">
            <a href="{{ route('admin.pembayaran.show', $pesanan->transaksi->id) }}" class="btn btn-outline">Lihat Nota Pembayaran</a>
        </div>
        @endif
    </div>
</div>
@endsection
