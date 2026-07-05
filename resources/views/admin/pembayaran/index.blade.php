@extends('layouts.app')
@section('title', 'Data Pembayaran')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Daftar Pembayaran</h3>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-warning">{{ session('info') }}</div>
        @endif

        <form method="GET" class="filter-bar">
            <div class="search-input-wrapper">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no nota / no pesanan / customer..." class="form-input">
            </div>
            <button type="submit" class="btn btn-outline">Cari</button>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No Nota</th>
                        <th>No Pesanan</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Total Bayar</th>
                        <th>Status Bayar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr>
                        <td class="font-medium">{{ $t->no_nota }}</td>
                        <td>{{ $t->pesanan->no_pesanan ?? '-' }}</td>
                        <td>{{ $t->pesanan->customer->nama ?? '-' }}</td>
                        <td>{{ $t->tanggal ? $t->tanggal->format('d M Y') : '-' }}</td>
                        <td>Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($t->status_bayar == 'lunas')
                                <span class="badge badge-lunas">Lunas</span>
                            @elseif($t->status_bayar == 'dp')
                                <span class="badge badge-dp">DP</span>
                            @elseif($t->status_bayar == 'belum_bayar')
                                <span class="badge badge-belum-bayar">Belum Bayar</span>
                            @else
                                <span class="badge">{{ ucfirst($t->status_bayar ?? '-') }}</span>
                            @endif
                        </td>
                        <td class="text-center table-actions" style="justify-content: center;">
                            <a href="{{ route('admin.pembayaran.show', $t->id) }}" class="text-primary">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">Tidak ada data pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $transaksis->links() }}</div>
    </div>
</div>
@endsection
