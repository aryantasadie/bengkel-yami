@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-icon yellow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-info">
            <div class="stat-card-value">{{ $pesananAktif ?? 0 }}</div>
            <div class="stat-card-label">Pesanan Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div class="stat-card-info">
            <div class="stat-card-value">{{ $pesananSelesai ?? 0 }}</div>
            <div class="stat-card-label">Pesanan Selesai</div>
        </div>
    </div>
    @if(auth()->user()->role === 'owner')
    <div class="stat-card">
        <div class="stat-card-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-card-info">
            <div class="stat-card-value">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-label">Pendapatan Bulan Ini</div>
        </div>
    </div>
    @endif
    <div class="stat-card">
        <div class="stat-card-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div class="stat-card-info">
            <div class="stat-card-value">{{ $karyawanAktif ?? 0 }}</div>
            <div class="stat-card-label">Karyawan Aktif</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Pesanan Terbaru</h3>
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline" style="font-size: 0.875rem; color: var(--primary); text-decoration: none;">Lihat Semua</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No Pesanan</th>
                        <th>Customer</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesananTerbaru ?? [] as $pesanan)
                    <tr>
                        <td class="font-medium">{{ $pesanan->no_pesanan }}</td>
                        <td>{{ $pesanan->customer->nama ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($pesanan->tanggal_masuk)->format('d M Y, H:i') }}</td>
                        <td>
                            @if($pesanan->status == 'antrian')
                                <span style="background: var(--warning-light); color: var(--warning); padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Antrian</span>
                            @elseif($pesanan->status == 'proses')
                                <span style="background: #DBEAFE; color: #1E40AF; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Proses</span>
                            @elseif($pesanan->status == 'selesai')
                                <span style="background: var(--success-light); color: var(--success); padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Selesai</span>
                            @else
                                <span style="background: var(--danger-light); color: var(--danger); padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Dibatalkan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" style="color: var(--primary); text-decoration: none; font-size: 14px;">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 2rem;">Belum ada pesanan terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
