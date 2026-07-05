@extends('layouts.app')
@section('title', 'Detail Customer')
@section('content')
<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3>Detail Customer: {{ $customer->nama }}</h3>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">Kembali</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="text-sm text-muted">Nama Customer</div>
                <div class="font-medium text-lg">{{ $customer->nama }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">No Telepon</div>
                <div class="font-medium">{{ $customer->no_telp ?? '-' }}</div>
            </div>
            <div>
                <div class="text-sm text-muted">Diskon Default</div>
                <div class="font-medium">{{ $customer->diskon_default }}%</div>
            </div>
            <div>
                <div class="text-sm text-muted">Alamat</div>
                <div>{{ $customer->alamat ?? '-' }}</div>
            </div>
        </div>
        
        <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--neutral-200);">Riwayat Pesanan</h4>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No Pesanan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->pesanan as $p)
                    <tr>
                        <td class="font-medium">{{ $p->no_pesanan }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_masuk)->format('d M Y') }}</td>
                        <td>{{ ucfirst($p->status) }}</td>
                        <td><a href="{{ route('admin.pesanan.show', $p->id) }}" class="text-primary">Lihat</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Belum ada riwayat pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection