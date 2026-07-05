@extends('layouts.app')
@section('title', 'Data Pesanan')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Pesanan</h3>
        <a href="{{ route('admin.pesanan.create') }}" class="btn btn-primary">+ Buat Pesanan</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Filter Tabs --}}
        <div class="filter-tabs" style="margin-bottom: 1rem;">
            <a href="{{ route('admin.pesanan.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'antrian']) }}" class="filter-tab {{ request('status') == 'antrian' ? 'active' : '' }}">Antrian</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'proses']) }}" class="filter-tab {{ request('status') == 'proses' ? 'active' : '' }}">Proses</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'selesai']) }}" class="filter-tab {{ request('status') == 'selesai' ? 'active' : '' }}">Selesai</a>
            <a href="{{ route('admin.pesanan.index', ['status' => 'dibatalkan']) }}" class="filter-tab {{ request('status') == 'dibatalkan' ? 'active' : '' }}">Dibatalkan</a>
        </div>

        {{-- Search --}}
        <form method="GET" class="filter-bar">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="search-input-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no pesanan / nama customer..." class="form-input">
            </div>
            <button type="submit" class="btn btn-outline">Cari</button>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No Pesanan</th>
                        <th>Customer</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $p)
                    <tr>
                        <td class="font-medium">{{ $p->no_pesanan }}</td>
                        <td>{{ $p->customer->nama ?? '-' }}</td>
                        <td>{{ $p->tanggal_masuk ? $p->tanggal_masuk->format('d M Y') : '-' }}</td>
                        <td>
                            @switch($p->status)
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
                                    <span class="badge">{{ ucfirst($p->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @php
                                $subtotal = $p->pesananJasa->sum('subtotal') + $p->pesananSparepart->sum('subtotal');
                                $nominalDiskon = $subtotal * ($p->diskon_persen / 100);
                                $sisa = $subtotal - $nominalDiskon - $p->dp;
                            @endphp
                            <div class="text-xs" style="line-height: 1.4;">
                                <div style="display: flex; justify-content: space-between; gap: 1rem;">
                                    <span class="text-muted">Total:</span> 
                                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($p->diskon_persen > 0)
                                <div style="display: flex; justify-content: space-between; gap: 1rem;">
                                    <span class="text-muted">Diskon ({{ $p->diskon_persen }}%):</span> 
                                    <span class="text-danger">- Rp {{ number_format($nominalDiskon, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                @if($p->dp > 0)
                                <div style="display: flex; justify-content: space-between; gap: 1rem;">
                                    <span class="text-muted">DP:</span> 
                                    <span class="text-success">- Rp {{ number_format($p->dp, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <div style="display: flex; justify-content: space-between; gap: 1rem; border-top: 1px dashed var(--neutral-300); margin-top: 2px; padding-top: 2px;">
                                    <strong style="color: var(--primary-600);">Sisa:</strong> 
                                    <strong style="color: var(--primary-600);">Rp {{ number_format(max(0, $sisa), 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </td>
                        <td class="text-center table-actions" style="justify-content: center;">
                            <a href="{{ route('admin.pesanan.show', $p->id) }}" class="text-primary">Detail</a>
                            @if(in_array($p->status, ['antrian', 'proses']))
                                | <a href="{{ route('admin.pesanan.edit', $p->id) }}" class="text-warning">Edit</a>
                            @endif
                            @if($p->status == 'antrian')
                                | <a href="#" class="text-danger" onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus pesanan ini?')) document.getElementById('delete-form-{{ $p->id }}').submit();">Hapus</a>
                                <form id="delete-form-{{ $p->id }}" action="{{ route('admin.pesanan.destroy', $p->id) }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Tidak ada data pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pesanans->links() }}</div>
    </div>
</div>
@endsection
